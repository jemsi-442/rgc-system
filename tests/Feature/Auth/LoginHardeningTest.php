<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginHardeningTest extends TestCase
{
    use RefreshDatabase;

    public function test_inactive_user_cannot_sign_in_through_web_login(): void
    {
        $this->seed(DatabaseSeeder::class);

        $user = User::query()->where('email', 'branchadmin@rgc.or.tz')->firstOrFail();
        $user->forceFill(['status' => 'inactive'])->save();

        $this->post(route('login.attempt'), [
            'email' => 'branchadmin@rgc.or.tz',
            'password' => 'ChangeMe123!',
        ])
            ->assertSessionHasErrors([
                'email' => 'Your account is inactive. Please contact church leadership.',
            ]);

        $this->assertGuest();
    }

    public function test_login_page_shows_seeded_role_credentials_in_testing_environment(): void
    {
        $this->seed(DatabaseSeeder::class);

        $this->get(route('login'))
            ->assertOk()
            ->assertSee('superadmin@rgc.or.tz')
            ->assertSee('regionaladmin@rgc.or.tz')
            ->assertSee('districtadmin@rgc.or.tz')
            ->assertSee('branchadmin@rgc.or.tz')
            ->assertSee('Local QA access');
    }

    public function test_seeded_role_dashboard_accounts_can_open_their_expected_dashboards(): void
    {
        $this->seed(DatabaseSeeder::class);

        $accounts = [
            'superadmin@rgc.or.tz' => 'Super Admin Workspace',
            'regionaladmin@rgc.or.tz' => 'Regional Admin Workspace',
            'districtadmin@rgc.or.tz' => 'District Admin Workspace',
            'branchadmin@rgc.or.tz' => 'Branch Admin Workspace',
        ];

        foreach ($accounts as $email => $expectedHeading) {
            $this->post(route('login.attempt'), [
                'email' => $email,
                'password' => 'ChangeMe123!',
            ])->assertRedirect(route('dashboard'));

            $this->get(route('dashboard'))
                ->assertOk()
                ->assertSee($expectedHeading);

            $this->post(route('logout'))->assertRedirect(route('home'));
            $this->assertGuest();
        }
    }
}
