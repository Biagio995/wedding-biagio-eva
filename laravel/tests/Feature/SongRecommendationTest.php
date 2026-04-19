<?php

namespace Tests\Feature;

use App\Http\Controllers\SongRecommendationController;
use App\Http\Controllers\WeddingController;
use App\Models\AuditLog;
use App\Models\Guest;
use App\Models\SongRecommendation;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class SongRecommendationTest extends TestCase
{
    use RefreshDatabase;

    private function loginAsAdmin(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));
        $this->post(route('admin.login'), ['password' => 'secret']);
    }

    public function test_guest_can_submit_a_song_suggestion_via_session(): void
    {
        $guest = Guest::query()->create(['name' => 'Eva', 'token' => 'tok-eva']);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post(route('wedding.songs.store'), [
                'title' => 'Don\'t Stop Me Now',
                'artist' => 'Queen',
                'notes' => 'first dance vibe',
            ])
            ->assertRedirect();

        $this->assertDatabaseCount('song_recommendations', 1);
        $song = SongRecommendation::query()->first();
        $this->assertSame($guest->id, $song->guest_id);
        $this->assertSame('Queen', $song->artist);
        $this->assertNull($song->submitted_by);
    }

    public function test_anonymous_visitor_must_provide_a_name(): void
    {
        $this->post(route('wedding.songs.store'), [
            'title' => 'Hey Jude',
            'artist' => 'The Beatles',
        ])->assertSessionHasErrors(['submitted_by'], null, 'songs');

        $this->assertDatabaseCount('song_recommendations', 0);
    }

    public function test_anonymous_visitor_with_name_can_submit(): void
    {
        $this->post(route('wedding.songs.store'), [
            'submitted_by' => 'Mario',
            'title' => 'Dancing Queen',
        ])->assertRedirect();

        $this->assertDatabaseHas('song_recommendations', [
            'submitted_by' => 'Mario',
            'title' => 'Dancing Queen',
            'guest_id' => null,
        ]);
    }

    public function test_guest_can_only_remove_their_own_song(): void
    {
        $owner = Guest::query()->create(['name' => 'Owner', 'token' => 'tok-owner']);
        $other = Guest::query()->create(['name' => 'Other', 'token' => 'tok-other']);

        $song = SongRecommendation::query()->create([
            'guest_id' => $owner->id,
            'title' => 'Mine',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $other->id])
            ->delete(route('wedding.songs.destroy', $song))
            ->assertForbidden();

        $this->assertDatabaseHas('song_recommendations', ['id' => $song->id]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $owner->id])
            ->delete(route('wedding.songs.destroy', $song))
            ->assertRedirect();

        $this->assertDatabaseMissing('song_recommendations', ['id' => $song->id]);
    }

    public function test_anonymous_user_can_remove_their_own_song_via_session_token(): void
    {
        $this->post(route('wedding.songs.store'), [
            'submitted_by' => 'Anon',
            'title' => 'Temporary',
        ])->assertRedirect();

        $song = SongRecommendation::query()->firstOrFail();
        $this->assertNotNull($song->session_token);

        $this->withSession([SongRecommendationController::SESSION_SONG_TOKEN => 'other-token'])
            ->delete(route('wedding.songs.destroy', $song))
            ->assertForbidden();

        $this->withSession([SongRecommendationController::SESSION_SONG_TOKEN => $song->session_token])
            ->delete(route('wedding.songs.destroy', $song))
            ->assertRedirect();

        $this->assertDatabaseMissing('song_recommendations', ['id' => $song->id]);
    }

    public function test_public_wedding_page_lists_own_song_recommendations(): void
    {
        Config::set('wedding.admin.password_hash', null);
        $guest = Guest::query()->create(['name' => 'Listener', 'token' => 'tok-list']);

        SongRecommendation::query()->create([
            'guest_id' => $guest->id,
            'title' => 'Starlight',
            'artist' => 'Muse',
        ]);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->get('/w')
            ->assertOk()
            ->assertSee('Your suggestions', false)
            ->assertSee('Starlight')
            ->assertSee('Muse');
    }

    public function test_public_feed_lists_every_recent_song_without_pii(): void
    {
        Config::set('wedding.admin.password_hash', null);

        SongRecommendation::query()->create([
            'submitted_by' => 'Maria Rossi',
            'title' => 'Africa',
            'artist' => 'Toto',
            'notes' => 'private note that must stay hidden',
            'session_token' => 'another-browser',
        ]);

        $response = $this->get('/w')->assertOk();
        $response->assertSee('Already suggested', false);
        $response->assertSee('Africa');
        $response->assertSee('Toto');
        /** First name only; surname and notes never leak in the public feed. */
        $response->assertSee('· Maria', false);
        $response->assertDontSee('Rossi');
        $response->assertDontSee('private note that must stay hidden');
    }

    public function test_admin_index_requires_auth_and_lists_songs(): void
    {
        Config::set('wedding.admin.password_hash', bcrypt('secret'));

        $this->get(route('admin.songs.index'))->assertRedirect(route('admin.login'));

        SongRecommendation::query()->create([
            'submitted_by' => 'Carla',
            'title' => 'Mr. Brightside',
            'artist' => 'The Killers',
        ]);

        $this->loginAsAdmin();

        $this->get(route('admin.songs.index'))
            ->assertOk()
            ->assertSee('Mr. Brightside')
            ->assertSee('The Killers')
            ->assertSee('Carla');
    }

    public function test_admin_can_delete_a_song_and_it_is_audited(): void
    {
        $song = SongRecommendation::query()->create([
            'submitted_by' => 'Ghost',
            'title' => 'Gone Song',
        ]);

        $this->loginAsAdmin();
        $this->delete(route('admin.songs.destroy', $song))
            ->assertRedirect(route('admin.songs.index'));

        $this->assertDatabaseMissing('song_recommendations', ['id' => $song->id]);
        $this->assertDatabaseHas('audit_logs', ['action' => 'song.deleted']);
    }

    public function test_admin_can_export_songs_as_csv(): void
    {
        SongRecommendation::query()->create([
            'submitted_by' => 'Anna',
            'title' => 'Africa',
            'artist' => 'Toto',
        ]);

        $this->loginAsAdmin();
        $response = $this->get(route('admin.songs.export'));
        $response->assertOk();
        $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');

        $body = $response->streamedContent();
        $this->assertStringStartsWith("\xEF\xBB\xBF", $body);
        $this->assertStringContainsString('id,submitted_by,guest_id,guest_name,title,artist,notes,created_at', $body);
        $this->assertStringContainsString('Africa', $body);
        $this->assertStringContainsString('Toto', $body);
        $this->assertStringContainsString('Anna', $body);

        $this->assertDatabaseHas('audit_logs', ['action' => 'song.csv.exported']);
    }

    public function test_feature_can_be_disabled_via_config(): void
    {
        Config::set('wedding.song_recommendations.enabled', false);
        Config::set('wedding.admin.password_hash', null);

        $response = $this->get('/w')->assertOk();
        $response->assertDontSee('Song suggestions for the DJ');
        $response->assertDontSee('dj-songs');
    }

    public function test_title_is_required(): void
    {
        $guest = Guest::query()->create(['name' => 'Needs', 'token' => 'tok-needs']);

        $this->withSession([WeddingController::SESSION_WEDDING_GUEST_ID => $guest->id])
            ->post(route('wedding.songs.store'), [
                'artist' => 'No Title',
            ])
            ->assertSessionHasErrors(['title'], null, 'songs');
    }
}
