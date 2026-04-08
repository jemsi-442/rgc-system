<?php

namespace Tests\Feature\SuperAdmin;

use App\Models\HomeSlider;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HomeSliderManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_super_admin_can_open_slider_pages_and_create_a_slide(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($admin)
            ->get(route('sliders.index'))
            ->assertOk()
            ->assertSeeText(__('Manage Homepage Slides'));

        $this->actingAs($admin)
            ->get(route('sliders.create'))
            ->assertOk()
            ->assertSeeText(__('Create Slide'));

        $response = $this->actingAs($admin)->post(route('sliders.store'), [
            'title' => 'National Revival Week',
            'subtitle' => 'Prayer and worship across Tanzania',
            'sort_order' => 3,
            'is_active' => '1',
            'image' => UploadedFile::fake()->image('revival.jpg', 1600, 900),
        ]);

        $response->assertRedirect(route('sliders.index'));

        $slider = HomeSlider::query()->firstOrFail();

        $this->assertSame('National Revival Week', $slider->title);
        $this->assertSame(3, $slider->sort_order);
        $this->assertTrue($slider->is_active);
        Storage::disk('public')->assertExists($slider->image_path);
    }

    public function test_super_admin_can_edit_a_slide_and_replace_its_image(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $oldPath = UploadedFile::fake()->image('old-slide.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'Old Title',
            'subtitle' => 'Old subtitle',
            'image_path' => $oldPath,
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($admin)
            ->get(route('sliders.edit', $slider))
            ->assertOk()
            ->assertSeeText(__('Edit Slide'));

        $response = $this->actingAs($admin)->put(route('sliders.update', $slider), [
            'title' => 'Updated Convention Slide',
            'subtitle' => 'Updated subtitle',
            'sort_order' => 8,
            'is_active' => '0',
            'image' => UploadedFile::fake()->image('new-slide.jpg', 1800, 900),
        ]);

        $response->assertRedirect(route('sliders.index'));

        $slider->refresh();

        $this->assertSame('Updated Convention Slide', $slider->title);
        $this->assertSame('Updated subtitle', $slider->subtitle);
        $this->assertSame(8, $slider->sort_order);
        $this->assertFalse($slider->is_active);
        $this->assertNotSame($oldPath, $slider->image_path);
        Storage::disk('public')->assertMissing($oldPath);
        Storage::disk('public')->assertExists($slider->image_path);
    }

    public function test_super_admin_can_toggle_slide_visibility_from_index(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $path = UploadedFile::fake()->image('toggle-me.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'Visibility Toggle',
            'subtitle' => 'Quick action slide',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 2,
        ]);

        $response = $this->actingAs($admin)->patch(route('sliders.status', $slider), [
            'is_active' => '0',
        ]);

        $response->assertRedirect(route('sliders.index'));
        $slider->refresh();

        $this->assertFalse($slider->is_active);
    }

    public function test_super_admin_can_update_slide_sort_order_from_index(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $path = UploadedFile::fake()->image('reorder.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'Sortable Slide',
            'subtitle' => 'Inline order update',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 9,
        ]);

        $response = $this->actingAs($admin)->patch(route('sliders.sort-order', $slider), [
            'sort_order' => 1,
        ]);

        $response->assertRedirect(route('sliders.index'));
        $slider->refresh();

        $this->assertSame(1, $slider->sort_order);
    }

    public function test_super_admin_can_delete_a_slide_and_its_image(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();
        $path = UploadedFile::fake()->image('delete-me.jpg')->store('sliders', 'public');

        $slider = HomeSlider::query()->create([
            'title' => 'Delete me',
            'subtitle' => 'Temporary slide',
            'image_path' => $path,
            'is_active' => true,
            'sort_order' => 4,
        ]);

        $response = $this->actingAs($admin)->delete(route('sliders.destroy', $slider));

        $response->assertRedirect(route('sliders.index'));
        $this->assertDatabaseMissing('slides', ['id' => $slider->id]);
        Storage::disk('public')->assertMissing($path);
    }

    public function test_branch_admin_cannot_access_slider_management_routes(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $branchAdmin = User::query()->create([
            'name' => 'Branch Slider Admin',
            'email' => 'branch.slider.admin@rgc.test',
            'password' => 'ChangeMe123!',
            'role' => 'branch_admin',
            'status' => 'active',
            'region_id' => $admin->region_id,
            'district_id' => $admin->district_id,
            'branch_id' => $admin->branch_id,
            'church_id' => $admin->church_id,
            'email_verified_at' => now(),
        ]);
        $branchAdmin->syncRoles(['branch_admin']);

        $slider = HomeSlider::query()->create([
            'title' => 'Protected Slide',
            'subtitle' => 'Only super admins may manage this.',
            'image_path' => UploadedFile::fake()->image('protected-slide.jpg')->store('sliders', 'public'),
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $this->actingAs($branchAdmin)
            ->get(route('sliders.index'))
            ->assertForbidden();

        $this->actingAs($branchAdmin)
            ->post(route('sliders.store'), [
                'title' => 'Unauthorized Slide',
                'subtitle' => 'Should not be created',
                'sort_order' => 3,
                'is_active' => '1',
                'image' => UploadedFile::fake()->image('unauthorized.jpg', 1600, 900),
            ])
            ->assertForbidden();

        $this->actingAs($branchAdmin)
            ->delete(route('sliders.destroy', $slider))
            ->assertForbidden();
    }

    public function test_super_admin_cannot_upload_svg_as_slider_image(): void
    {
        Storage::fake('public');
        $this->seed(DatabaseSeeder::class);

        $admin = User::query()->where('email', 'superadmin@rgc.or.tz')->firstOrFail();

        $this->actingAs($admin)
            ->from(route('sliders.create'))
            ->post(route('sliders.store'), [
                'title' => 'SVG Slide Attempt',
                'subtitle' => 'Should be rejected',
                'sort_order' => 5,
                'is_active' => '1',
                'image' => UploadedFile::fake()->create('hero.svg', 12, 'image/svg+xml'),
            ])
            ->assertRedirect(route('sliders.create'))
            ->assertSessionHasErrors(['image']);

        $this->assertDatabaseMissing('slides', [
            'title' => 'SVG Slide Attempt',
        ]);
    }
}
