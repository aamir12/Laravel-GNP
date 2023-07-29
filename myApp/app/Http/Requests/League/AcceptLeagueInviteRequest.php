<?php

namespace App\Http\Requests\League;

use Illuminate\Foundation\Http\FormRequest;
use Auth;
class AcceptLeagueInviteRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'league_id' => 'required|
                            exists:leagues,id,deleted_at,NULL|
                            exists:league_invites,league_id,accepted,0,rejected,0,invitee_id,' . Auth::id()
        ];
    }
}
