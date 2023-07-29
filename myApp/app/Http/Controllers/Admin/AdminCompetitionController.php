<?php

namespace App\Http\Controllers\Admin;

use App\Classes\CompetitionManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Competition\CreateCompetitionRequest;
use App\Http\Requests\Competition\UpdateCompetitionRequest;
use App\Models\Competition;
use App\Models\Meta;
use App\Services\StorageService;
use Illuminate\Http\Request;

/**
 * @group Admin API - Competitions
 *
 * Admin APIs for managing competitions.
 *
 * Competitions allow users to win prizes and get rewarded for reaching specified
 * KPI goals.
 */
class AdminCompetitionController extends Controller
{
    /**
     * Create Competition
     *
     * Creates a new competition with the values provided.
     *
     * @bodyParam name string required Max: 191. The name of the competition. Example: Awesome Comp
     * @bodyParam description string A description of the competition. Example: The world\'s greatest competition!
     *
     * @bodyParam type string The type of competition to create. Must be one of:
     * Fixed/Rolling. When a rolling competition ends, a new one will be
     * automatically created with all the same settings and entrants. A rolling
     * competition will continue to repeat in this way until manually stopped.
     * Example: Fixed
     *
     * @bodyParam score_threshold double required The minimum score a competitor must reach to be eligible to win. Example: 100
     * @bodyParam threshold_operator string The comparison operator to use when comparing score values against this competition's `score_threshold`. Must be one of: `<`/`<=`/`==`/`>=`/`>`. Default is `>=`. Example: <
     * @bodyParam start_date datetime required The competition's start date and time. Example: 2019-10-22 13:15
     * @bodyParam end_date datetime Required if `period` is not provided. The competition's end date and time. Example: 2019-10-23 13:15
     * @bodyParam period string Required if `end_date` is not provided. The value of `end_date` will be calculated as the `start_date` plus the `period`. Must be one of: daily/weekly/monthly. Example: daily
     * @bodyParam is_lottery bool Whether or not the competition is a lottery.
     * @bodyParam space_count int Number of entrants allowed into the competition. Set this to 0 to allow infinite entrants.
     * @bodyParam entry_fee float The competition's entry fee. Example: 9.99
     * @bodyParam terms_url url A url to the competition's terms and conditions. Example: https://www.example.com
     * @bodyParam groups int[] An array of group IDs. If specified, only users belonging to these groups will be eligible to enter the competition. Example: [1, 2, 3]
     * @bodyParam auto_enter_user int Whether or not to automatically enter eligible users into the competition. Example: 0
     * @bodyParam image file required A display image for the competition.
     * @bodyParam status string The competition's status. Must be one of: live/draft/archived. Example: live
     * @bodyParam metadata json Metadata for the competition. Example: [{"title": "Competition Title"}, {"url": "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/Admin/Competitions/create.json
     */
    public function create(CreateCompetitionRequest $req)
    {
        $competition = CompetitionManager::create($req->validated());
        return response()->success(__('competition')['created_success'], $competition);
    }

    /**
     * Update Competition
     *
     * Updates the specified competition using the values provided.
     *
     * @bodyParam id integer required Competition ID. Example: 1
     * @bodyParam name string required Max: 191. The name of the competition. Example: Awesome Comp
     * @bodyParam description string A description of the competition. Example: The world\'s greatest competition!
     * @bodyParam type string The type of competition to create. Must be one of: Fixed/Rolling. Example: Fixed
     * @bodyParam score_threshold double required The minimum score a competitor must reach to be eligible to win. Example: 100
     * @bodyParam threshold_operator string The comparison operator to use when comparing score values against this competition's `score_threshold`. Must be one of: `<`/`<=`/`==`/`>=`/`>`. Example: <
     * @bodyParam start_date datetime required The competition's start date and time. Example: 2019-10-22 13:15
     * @bodyParam end_date datetime Required if `period` is not provided. The competition's end date and time. Example: 2019-10-23 13:15
     * @bodyParam period string Required if `end_date` is not provided. The value of `end_date` will be calculated as the `start_date` plus the `period`. Must be one of: daily/weekly/monthly. Example: daily
     * @bodyParam is_lottery bool Whether or not the competition is a lottery.
     * @bodyParam space_count int Number of entrants allowed into the competition. Set this to 0 to allow infinite entrants.
     * @bodyParam entry_fee float The competition's entry fee. Example: 9.99
     * @bodyParam terms_url url A url to the competition's terms and conditions. Example: https://www.example.com
     * @bodyParam groups int[] An array of group IDs. If specified, only users belonging to these groups will be eligible to enter the competition. Example: [1, 2, 3]
     * @bodyParam auto_enter_user int Whether or not to automatically enter eligible users into the competition. Example: 0
     * @bodyParam image file required A display image for the competition.
     * @bodyParam status string The competition's status. Must be one of: live/draft/archived. Example: live
     * @bodyParam metadata json Metadata for the competition. Example: [{"title": "Competition Title"}, {"url": "https://www.example.com"}]
     *
     * @responseFile 200 resources/responses/Admin/Competitions/update.json
     */
    public function update(UpdateCompetitionRequest $req)
    {
        $competition = Competition::find($req->id);
        $data = $req->validated();

        if (isset($data['image'])) {
            $data = array_merge($data, StorageService::storeImage($data['image']));
        }

        $competition->fill($data);

        if ($competition->isDirty('period')) {
            $competition->end_date = $competition->calcEndDate();
        }

        if ($competition->state === 'started' &&
            $competition->isDirty() &&
            $competition->isClean('type')
        ) {
            return response()->error(__('competition')['already_started']);
        }

        if (isset($data['groups'])) {
            $competition->groups()->sync($data['groups']);
        }

        $competition->save();
        $competition->setMetadata(Meta::extractMetadata($data));
        $competition->load('groups', 'meta');

        return $competition->wasChanged() || isset($data['groups'])
            ? response()->success(__('competition')['updated_success'], $competition)
            : response()->success(__('nothing_updated'), $competition);
    }

    /**
     * List Competitions
     *
     * Lists all competitions.
     *
     * @responseFile 200 resources/responses/Admin/Competitions/list.json
     */
    public function list()
    {
        $competitions = Competition::with('prizes.stock')
                                   ->with('groups')
                                   ->withCount('entrants')
                                   ->get();

        return response()->success(__('competition')['list_success'], $competitions);
    }

    /**
     * List Competition Entrants
     *
     * Gets a competition along with the users who have been entered into it.
     *
     * @bodyParam id int required The ID of the compeition.
     * @bodyParam winner bool Set this to true to retrieve only the winner of the competition.
     *
     * @responseFile 200 resources/responses/Admin/Competitions/entrants.json
     */
    public function getWithEntrants(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:competitions',
            'winner' => 'boolean',
        ]);
        $competition = CompetitionManager::competitionListEntrants($req);
        return response()->success(__('competition')['list_success'], $competition);
    }

    /**
     * Get Competition
     *
     * Gets the competition specified by `id`.
     *
     * @bodyParam id int required The ID of the competition. Example: 1
     *
     * @responseFile 200 resources/responses/Admin/Competitions/get.json
     */
    public function get(Request $req)
    {
        $req->validate(['id' => 'required|exists:competitions']);
        $competition = Competition::with(['prizes', 'prizes.stock'])
                                  ->with('groups')
                                  ->withCount('entrants')
                                  ->find($req->id);

        return response()->success(__('competition')['list_success'], $competition);
    }
}
