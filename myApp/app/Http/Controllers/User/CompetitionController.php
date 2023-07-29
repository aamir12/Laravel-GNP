<?php

namespace App\Http\Controllers\User;

use App\Classes\CompetitionManager;
use App\Http\Controllers\Controller;
use App\Http\Requests\Competition\EnterCompetitionRequest;
use App\Http\Requests\Competition\EnterLotteryRequest;
use App\Models\Competition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - Competitions
 *
 * User APIs for interacting with competitions.
 *
 * Users can enter competitions, get information about competitions that are
 * relevant to them, and reveal competition results.
 */
class CompetitionController extends Controller
{
    /**
     * Enter Competition
     *
     * Enters the currently authenticated user into the specified competition.
     *
     * @bodyParam id int required The ID of the competition to enter.
     *
     * @responseFile 200 resources/responses/User/Competition/enter-competition.json
     */
    public function enterCompetition(EnterCompetitionRequest $req)
    {
        $user = Auth::user();
        $competition = Competition::find($req->id);

        if ($competition->entrants()->where('user_id', $user->id)->exists()) {
            return response()->error(__('competition')['already_entered']);
        }

        if (!$competition->isUserEligibleToEnter($user)) {
            return response()->error(__('competition')['user_not_in_group']);
        }

        $user->enterCompetition($competition);
        return response()->success(__('competition')['user_lottery_success']);
    }

    /**
     * Enter Lottery
     *
     * Enters the currently authenticated user into the specified lottery.
     *
     * @bodyParam id int required The ID of the lottery to enter.
     *
     * @responseFile 200 resources/responses/User/Competition/enter-lottery.json
     */
    public function enterLottery(EnterLotteryRequest $req)
    {
        $user = Auth::user();
        $competition = Competition::find($req->id);

        if ($competition->entrants()->where('user_id', $user->id)->exists()) {
            return response()->error(__('competition')['already_entered']);
        }

        if (!$competition->isUserEligibleToEnter($user)) {
            return response()->error(__('competition')['user_not_in_group']);
        }

        $user->enterCompetition($competition);
        return response()->success(__('competition')['user_lottery_success']);
    }

    /**
     * List User's Lotteries
     *
     * Lists the lotteries that currently authenticated user is entered in.
     *
     * @responseFile 200 resources/responses/User/Competition/list-lotteries.json
     */
    public function listLotteries()
    {
        $competitions = Auth::user()->competitions()->where('is_lottery', true)->get();
        return response()->success(__('competition')['list_lottery_success'], $competitions);
    }

    /**
     * List User's Competitions
     *
     * Lists the non-lottery competitions that currently authenticated user is entered in.
     *
     * @responseFile 200 resources/responses/User/Competition/list-competitions.json
     */
    public function listCompetitions()
    {
        $competitions = Auth::user()->competitions()->where('is_lottery', false)->get();
        return response()->success(__('competition')['list_success'], $competitions);
    }

    /**
     * Reveal Competition
     *
     * Reveals whether or not the currently authenticated user has won the specified competition.
     *
     * @bodyParam id int required The ID of the competition. Example: 1
     *
     * @responseFile 200 resources/responses/User/Competition/reveal-competition.json
     */
    public function revealCompetition(Request $req)
    {
        $req->validate(['id' => 'required|exists:competitions,id,state,ended,status,archived']);

        $competition = Competition::find($req->id);
        $entrant = $competition->entrants()->find(Auth::id());

        if (!$entrant) {
            return response()->error(__('competition')['user_not_in_competition']);
        }

        if ($entrant->pivot->competition_revealed) {
            return response()->error(__('competition')['already_revealed']);
        }

        $winner = Auth::user()->revealCompetition($competition);

        return response()->success(
            __('competition')['revealed_success'],
            [
                'is_winner' => isset($winner),
                'prize' => isset($winner) ? $winner->prize : null
            ]
        );
    }

    /**
     * Lotteries Summary
     *
     * Provides a summary of lotteries including upcoming, running, open and unrevealed lotteries.
     *
     * @responseFile 200 resources/responses/User/Competition/lotteries-summary.json
     */
    public function lotterySummary()
    {
        $user = Auth::user();
        $lotteries = [
            'upcoming' => $user->upcomingLotteries(),
            'running' => $user->runningLotteries(),
            'open' => $user->openLotteries(),
            'unrevealed' => $user->unrevealedLotteries(),
        ];
        return response()->success(__('competition')['summary_success'], $lotteries);
    }

    /**
     * List User's Available Open Lotteries
     *
     * Lists the lottery competitions that user can enter.
     *
     * @responseFile 200 resources/responses/User/Competition/list-available-open-lotteries.json
     */
    public function listOpenLotteries()
    {
        $competitions = Auth::user()->openLotteries();
        return response()->success(__('competition')['list_lottery_success'], $competitions);
    }

    /**
     * List User's Running Lotteries
     *
     *  Lists the lotteries that currently authenticated user is entered in.
     *
     * @responseFile 200 resources/responses/User/Competition/list-running-lotteries.json
     */
    public function listRunningLotteries()
    {
        $competitions = Auth::user()->runningLotteries();
        return response()->success(__('competition')['list_lottery_success'], $competitions);
    }

    /**
     * List User's Closed Lotteries
     *
     *  Lists the lotteries that currently authenticated user is entered in.
     *
     * @responseFile 200 resources/responses/User/Competition/list-closed-lotteries.json
     */
    public function listClosedLotteries()
    {
        $competitions = Auth::user()->closedLotteries();
        return response()->success(__('competition')['list_lottery_success'], $competitions);
    }

    /**
     * List User's Unrevealed Lotteries
     *
     *  Lists the lotteries that currently authenticated user is entered in.
     *
     * @responseFile 200 resources/responses/User/Competition/list-unrevealed-lotteries.json
     */
    public function listUnrevealedLotteries()
    {
        $competitions = Auth::user()->unrevealedLotteries();
        return response()->success(__('competition')['list_lottery_success'], $competitions);
    }
}
