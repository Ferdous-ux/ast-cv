<?php

namespace Tests\Feature;

use App\Http\Middleware\AdminMiddleware;
use App\Models\Resume;
use App\Models\ResumeVersion;
use App\Models\Skill;
use App\Models\SkillAlias;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminSkillTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        /*
         * نعطل فقط Middleware الخاص بالأدمن.
         * ونترك Middleware الخاصة بالويب وRoute Model Binding تعمل.
         */
        $this->withoutMiddleware(
            AdminMiddleware::class
        );

        $this->actingAs(
            User::factory()->create()
        );
    }


    public function test_admin_can_open_skills_index(): void
    {
        Skill::create([
            'name' => 'Laravel',
            'category' => 'Programming',
        ]);

        $response = $this->get(
            route('admin.skills.index')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.skills.index')
            ->assertSee('Laravel');

        $this->assertDatabaseHas(
            'skills',
            [
                'name' => 'Laravel',
                'category' => 'Programming',
            ]
        );
    }


    public function test_admin_can_open_create_skill_page(): void
    {
        $response = $this->get(
            route('admin.skills.create')
        );

        $response
            ->assertOk()
            ->assertViewIs('admin.skills.create');
    }


    public function test_admin_can_create_skill_with_aliases(): void
    {
        $response = $this->post(
            route('admin.skills.store'),
            [
                'name' => 'JavaScript',
                'category' => 'Programming',

                'aliases_text' => implode(
                    PHP_EOL,
                    [
                        'JS | en',
                        'Java Script | en',
                        'جافاسكربت | ar',
                    ]
                ),
            ]
        );

        $response->assertRedirect(
            route('admin.skills.index')
        );

        $skill = Skill::query()
            ->where('name', 'JavaScript')
            ->firstOrFail();

        $this->assertDatabaseHas(
            'skills',
            [
                'id' => $skill->id,
                'name' => 'JavaScript',
                'category' => 'Programming',
            ]
        );

        $this->assertDatabaseHas(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
                'alias' => 'JS',
                'normalized_alias' => 'js',
                'language' => 'en',
            ]
        );

        $this->assertDatabaseHas(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
                'alias' => 'Java Script',
                'normalized_alias' => 'java script',
                'language' => 'en',
            ]
        );

        $this->assertDatabaseHas(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
                'alias' => 'جافاسكربت',
                'normalized_alias' => 'جافاسكربت',
                'language' => 'ar',
            ]
        );

        $this->assertSame(
            3,
            SkillAlias::where(
                'skill_id',
                $skill->id
            )->count()
        );
    }


    public function test_skill_name_must_be_unique(): void
    {
        Skill::create([
            'name' => 'PHP',
            'category' => 'Programming',
        ]);

        $response = $this
            ->from(route('admin.skills.create'))
            ->post(
                route('admin.skills.store'),
                [
                    'name' => 'PHP',
                    'category' => 'Backend',
                    'aliases_text' => '',
                ]
            );

        $response
            ->assertRedirect(
                route('admin.skills.create')
            )
            ->assertSessionHasErrors('name');

        $this->assertSame(
            1,
            Skill::where(
                'name',
                'PHP'
            )->count()
        );
    }


    public function test_alias_cannot_belong_to_two_different_skills(): void
    {
        $javascript = Skill::create([
            'name' => 'JavaScript',
            'category' => 'Programming',
        ]);

        SkillAlias::create([
            'skill_id' => $javascript->id,
            'alias' => 'JS',
            'normalized_alias' => 'js',
            'language' => 'en',
        ]);

        $response = $this
            ->from(route('admin.skills.create'))
            ->post(
                route('admin.skills.store'),
                [
                    'name' => 'Another Skill',
                    'category' => 'Programming',
                    'aliases_text' => 'JS | en',
                ]
            );

        $response
            ->assertRedirect(
                route('admin.skills.create')
            )
            ->assertSessionHasErrors(
                'aliases_text'
            );

        $this->assertDatabaseMissing(
            'skills',
            [
                'name' => 'Another Skill',
            ]
        );
    }


    public function test_admin_can_search_skill_by_alias(): void
    {
        $skill = Skill::create([
            'name' => 'JavaScript',
            'category' => 'Programming',
        ]);

        SkillAlias::create([
            'skill_id' => $skill->id,
            'alias' => 'JS',
            'normalized_alias' => 'js',
            'language' => 'en',
        ]);

        Skill::create([
            'name' => 'Python',
            'category' => 'Programming',
        ]);

        $response = $this->get(
            route(
                'admin.skills.index',
                [
                    'search' => 'JS',
                ]
            )
        );

        $response
            ->assertOk()
            ->assertSee('JavaScript')
            ->assertDontSee('Python');
    }


    public function test_admin_can_update_skill_and_aliases(): void
    {
        $skill = Skill::create([
            'name' => 'Node',
            'category' => 'Backend',
        ]);

        SkillAlias::create([
            'skill_id' => $skill->id,
            'alias' => 'Node JS',
            'normalized_alias' => 'node js',
            'language' => 'en',
        ]);

        $response = $this->put(
            route(
                'admin.skills.update',
                $skill
            ),
            [
                'name' => 'Node.js',
                'category' => 'Backend Development',

                'aliases_text' => implode(
                    PHP_EOL,
                    [
                        'NodeJS | en',
                        'Node JS | en',
                    ]
                ),
            ]
        );

        $response->assertRedirect(
            route('admin.skills.index')
        );

        $this->assertDatabaseHas(
            'skills',
            [
                'id' => $skill->id,
                'name' => 'Node.js',
                'category' => 'Backend Development',
            ]
        );

        $this->assertDatabaseHas(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
                'alias' => 'NodeJS',
                'normalized_alias' => 'nodejs',
                'language' => 'en',
            ]
        );

        $this->assertDatabaseHas(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
                'alias' => 'Node JS',
                'normalized_alias' => 'node js',
                'language' => 'en',
            ]
        );

        $this->assertSame(
            2,
            SkillAlias::where(
                'skill_id',
                $skill->id
            )->count()
        );
    }


    public function test_admin_can_delete_unused_skill(): void
    {
        $skill = Skill::create([
            'name' => 'Unused Skill',
            'category' => 'Other',
        ]);

        SkillAlias::create([
            'skill_id' => $skill->id,
            'alias' => 'Unused Alias',
            'normalized_alias' => 'unused alias',
            'language' => 'en',
        ]);

        $response = $this->delete(
            route(
                'admin.skills.destroy',
                $skill
            )
        );

        $response->assertRedirect(
            route('admin.skills.index')
        );

        $this->assertDatabaseMissing(
            'skills',
            [
                'id' => $skill->id,
            ]
        );

        $this->assertDatabaseMissing(
            'skill_aliases',
            [
                'skill_id' => $skill->id,
            ]
        );
    }


    public function test_skill_used_in_resume_cannot_be_deleted(): void
    {
        $user = User::factory()->create();

        $resume = Resume::create([
            'user_id' => $user->id,
            'title' => 'Developer Resume',
            'status' => 'draft',
        ]);

        $version = ResumeVersion::create([
            'resume_id' => $resume->id,
            'version_number' => 1,
            'status' => 'draft',
            'summary' => 'Developer summary',
            'template' => 'default',
        ]);

        $skill = Skill::create([
            'name' => 'Laravel',
            'category' => 'Programming',
        ]);

        $version->skills()->attach(
            $skill->id,
            [
                'level' => 'advanced',
                'sort_order' => 0,
            ]
        );

        $response = $this->delete(
            route(
                'admin.skills.destroy',
                $skill
            )
        );

        $response
            ->assertRedirect(
                route('admin.skills.index')
            )
            ->assertSessionHas('error');

        $this->assertDatabaseHas(
            'skills',
            [
                'id' => $skill->id,
                'name' => 'Laravel',
            ]
        );

        $this->assertDatabaseHas(
            'resume_skills',
            [
                'resume_version_id' => $version->id,
                'skill_id' => $skill->id,
            ]
        );
    }
}