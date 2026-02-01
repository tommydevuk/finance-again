<?php

namespace Tests\Browser;

use Illuminate\Support\Str;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ProjectManagementTest extends DuskTestCase
{
    /**
     * Test that a user can register, create a project, and manage its members.
     */
    public function test_can_create_project_and_manage_members(): void
    {
        $this->browse(function (Browser $browser) {
            $email = 'test-' . Str::random(8) . '@example.com';
            $projectName = 'Dusk Project ' . Str::random(4);

            $browser->visit('/register')
                    ->type('name', 'Dusk User')
                    ->type('email', $email)
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->press('REGISTER')
                    ->waitForLocation('/dashboard')
                    ->assertSee("Dusk User's Team")
                    // Navigate to the team
                    ->clickLink("Dusk User's Team")
                    ->waitForText('Team Overview')
                    // Navigate to projects
                    ->clickLink('Manage Projects')
                    ->waitForText('Manage team projects')
                    // Create a project
                    ->clickLink('Add Project')
                    ->waitForText('Create New Project')
                    ->type('name', $projectName)
                    ->type('description', 'A project created by an automated test.')
                    ->press('Create Project')
                    ->waitForText('Project created successfully')
                    ->assertSee($projectName)
                    // Open dropdown menu for the project
                    ->click('button[class*="h-8 w-8"]') 
                    ->waitForText('Manage Members')
                    ->clickLink('Manage Members')
                    // Verify the page loaded correctly without 500 error
                    ->waitForText('Manage Members')
                    ->assertSee('Control who can access and edit ' . $projectName);
        });
    }
}