<?php

namespace Rappasoft\LaravelLivewireTables\Tests\Visuals;

use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Rappasoft\LaravelLivewireTables\Tests\Http\Livewire\PetsTable;
use Rappasoft\LaravelLivewireTables\Tests\TestCase;

#[Group('Visuals')]
final class SortingVisualsTest extends TestCase
{
    public array $default10 = [];

    public array $asortNames = [];

    public array $rsortNames = [];

    public function test_array_setup(): array
    {
        $rSortNames = $aSortNames = $petNames = ['Cartman', 'Tux', 'May', 'Ben', 'Chico'];
        asort($aSortNames);
        rsort($rSortNames);

        $this->default10 = array_slice($petNames, 0, 10);
        $this->asortNames = array_slice($aSortNames, 0, 10);
        $this->rsortNames = array_slice($rSortNames, 0, 10);

        $this->assertNotEmpty($petNames);

        return $petNames;
    }

    public function test_th_headers_are_buttons_with_sorting_enabled(): void
    {
        Livewire::test(PetsTable::class)
            ->assertSeeHtmlInOrder([
                'wire:click="sortBy(\'id\')"',
                'class="text-gray-500 dark:text-gray-400 flex items-center space-x-1 text-left text-xs leading-4 font-medium uppercase tracking-wider group focus:outline-none"',
            ]);
    }

    public function test_th_headers_are_not_buttons_with_sorting_disabled(): void
    {
        Livewire::test(PetsTable::class)
            ->call('setSortingDisabled')
            ->assertDontSeeHtml('<button
                wire:click="sortBy(\'id\')"
                class="flex items-center space-x-1 text-left text-xs leading-4 font-medium uppercase tracking-wider group focus:outline-none text-gray-500 dark:text-gray-400"
            >');

    }

    public function test_th_headers_are_not_buttons_until_sorting_enabled(): void
    {
        Livewire::test(PetsTable::class)
            ->call('setSortingDisabled')
            ->assertDontSeeHtml('<button
                wire:click="sortBy(\'id\')"
                class="text-gray-500 dark:text-gray-400 flex items-center space-x-1 text-left text-xs leading-4 font-medium uppercase tracking-wider group focus:outline-none"
            >')
            ->call('setSortingEnabled')
            ->assertSeeHtmlInOrder([
                'wire:click="sortBy(\'id\')"',
                'class="text-gray-500 dark:text-gray-400 flex items-center space-x-1 text-left text-xs leading-4 font-medium uppercase tracking-wider group focus:outline-none"',
            ]);

    }

    public function test_th_headers_are_not_buttons_unless_the_column_is_sortable(): void
    {
        Livewire::test(PetsTable::class)
            ->assertDontSeeHtml('<button
                wire:click="sortBy(\'other\')"
                class="flex items-center space-x-1 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider group focus:outline-none dark:text-gray-400"
            >');
    }

    /** Needs updating for hero */
    /* public function test_th_header_icons_correct_based_on_sort_status(): void
     {
         Livewire::test(PetsTable::class)
             ->assertSeeHtml('<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>')
             ->call('setSort', 'name', 'asc')
             ->assertSeeHtmlInOrder([
                 '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>',
                 '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>',
             ])
             ->call('setSort', 'name', 'desc')
             ->assertSeeHtmlInOrder([
                 '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />',
                 '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>',
             ]);
     }*/

    public function test_sorting_pill_shows_when_enabled(): void
    {
        Livewire::test(PetsTable::class)
            ->call('setSort', 'name', 'asc')
            ->assertSee('Applied Sorting:');
    }

    public function test_sorting_pill_doesnt_shows_when_disabled(): void
    {
        Livewire::test(PetsTable::class)
            ->call('setSort', 'name', 'asc')
            ->call('setSortingPillsDisabled')
            ->assertDontSee('Applied Sorting:');
    }

    public function test_sorting_pills_only_show_if_there_are_sorts(): void
    {
        Livewire::test(PetsTable::class)
            ->assertDontSee('Applied Sorting:')
            ->call('setSort', 'name', 'asc')
            ->assertSee('Applied Sorting:');
    }

    public function test_only_one_sorting_pill_shows_with_single_column_on(): void
    {
        Livewire::test(PetsTable::class)
            ->call('sortBy', 'id')
            ->assertSee('Key: 0-9')
            ->call('sortBy', 'name')
            ->assertSee('Name: A-Z')
            ->assertDontSee('Key: 0-9');
    }

    public function test_multiple_sorting_pill_shows_with_single_column_off(): void
    {
        Livewire::test(PetsTable::class)
            ->call('setSingleSortingDisabled')
            ->call('sortBy', 'id')
            ->call('sortBy', 'name')
            ->assertSee('Name: A-Z')
            ->assertSee('Key: 0-9');
    }

    public function test_sorting_pill_shows_correct_name_and_direction(): void
    {
        Livewire::test(PetsTable::class)
            ->call('sortBy', 'id')
            ->assertSee('Key')
            ->assertSee('0-9')
            ->call('sortBy', 'id')
            ->assertSee('Key')
            ->assertSee('9-0');
    }

    public function test_sorting_pills_clear_button_shows_and_functions(): void
    {
        Livewire::test(PetsTable::class)
            ->call('sortBy', 'name')
            ->assertSee('Name: A-Z')
            ->call('clearSort', 'name')
            ->assertDontSee('Name: A-Z');
    }

    public function test_sorting_pills_dont_show_for_unknown_columns(): void
    {
        Livewire::test(PetsTable::class)
            ->call('sortBy', 'name2')
            ->assertDontSee('Name2: A-Z');
    }

    #[Depends('test_array_setup')]
    public function test_default_sorting_gets_applied_if_set_and_there_are_no_sorts(array $petNames): void
    {
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setDefaultSort', 'name', 'desc')
            ->assertSeeInOrder($this->rsortNames);
    }

    #[Depends('test_array_setup')]
    public function test_sort_direction_can_only_be_asc_or_desc(array $petNames): void
    {
        // If not asc, desc, default to asc
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setSort', 'name', 'ugkugkuh')
            ->assertSeeInOrder($this->asortNames);

        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setSort', 'name', 'desc')
            ->assertSeeInOrder($this->rsortNames);
    }

    #[Depends('test_array_setup')]
    public function test_skip_sorting_column_if_it_does_not_have_a_field(array $petNames): void
    {
        // Other col is a label therefore has no field
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setSort', 'other', 'desc')
            ->assertSeeInOrder($this->default10);
    }

    #[Depends('test_array_setup')]
    public function test_skip_sorting_column_if_it_is_not_sortable(array $petNames): void
    {
        // Other col is a label therefore is not sortable
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setSort', 'other', 'desc')
            ->assertSeeInOrder($this->default10);
    }

    #[Depends('test_array_setup')]
    public function test_sort_field_and_direction_are_applied_if_no_sort_callback(array $petNames): void
    {
        // TODO: Test that there is no callback
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->call('setSort', 'name', 'desc')
            ->assertSeeInOrder($this->rsortNames);
    }

    /**
     * @test
     *
     * @depends testArraySetup
     */
    /*public function test_sort_events_apply_correctly(): void
    {
        Livewire::test(PetsTable::class)
            ->assertSeeInOrder($this->default10)
            ->dispatch('set-sort', 'name', 'desc')
            ->assertDispatched('set-sort', 'name', 'desc')
            ->assertSeeInOrder($this->rsortNames)
            ->dispatch('clear-sorts')
            ->assertDispatched('clear-sorts')
            ->assertSeeInOrder($this->default10)
            ->dispatch('set-sort', 'name', 'ugkugkuh')
            ->assertDispatched('set-sort', 'name', 'ugkugkuh')
            ->assertSeeInOrder($this->asortNames);
    }*/
}
