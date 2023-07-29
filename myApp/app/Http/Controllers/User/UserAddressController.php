<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserAddress\CreateUserAddressRequest;
use App\Http\Requests\UserAddress\UpdateUserAddressRequest;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group User API - User Addresses
 *
 * User APIs for managing addresses.
 *
 * Users can store one or more addresses in the system. One address must always
 * be set as the default address. The default address will be used as the
 * shipping address whenever the user wins a physical prize unless otherwise
 * specified.
 */
class UserAddressController extends Controller
{
    /**
     * Create User Address
     *
     * Creates a new address for the currently authenticated user using the values provided.
     *
     * @bodyParam name string required The name on the address. Example: John
     * @bodyParam phone string The contact number. Example: 01643 214201
     * @bodyParam address_line_1 string First line of the address. Example: 21a Edgewood House
     * @bodyParam address_line_2 string Second line of the address. Example: Greenbank Estate
     * @bodyParam address_line_3 string Third line of the address. Example: Foxhayes
     * @bodyParam town string required The town of the address. Example: Exeter
     * @bodyParam county string The town of the address. Example: Devon
     * @bodyParam postcode string required The postcode of the address. Example EX16 5BH
     * @bodyParam country string required The country of address. Example United Kingdom
     * @bodyParam delivery_instructions string Any additional instructions for deliveries to this address. Example: Leave parcels behind the shed.
     *
     * @responseFile 200 resources/responses/User/UserAddress/create.json
     */
    public function create(CreateUserAddressRequest $req)
    {
        $userId = Auth::user()->id;
        $data = $req->validated();
        $data['is_default'] = 1;
        $data['user_id'] = $userId;
        if (UserAddress::where('user_id', $userId)->exists()) {
            $data['is_default'] = 0;
        }
        $userAddress = UserAddress::create($data);
        if ($userAddress) {
            return response()->success(__('user_address')['create_success'], $userAddress->getAttributes());
        }
        return response()->error(__('user_address')['create_error']);
    }

    /**
     * Get User Address
     *
     * Retrieves the user address specified by `id`.
     *
     * @bodyParam id integer required The ID of the user address. Example: 1
     *
     * @responseFile 200 resources/responses/User/UserAddress/get-user-address.json
     */
    public function get(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:user_addresses,id,user_id,' . Auth::id(),
        ]);
        $address = UserAddress::find($req->id);
        return response()->success(__('user_address')['found_success'], $address);
    }

    /**
     * List User Addresses
     *
     * Lists all addresses belonging to the currently authenticated user.
     *
     * @responseFile 200 resources/responses/User/UserAddress/list-user-addresses.json
     */
    public function list()
    {
        $addresses = UserAddress::where('user_id', Auth::id())->get();
        return response()->success(__('user_address')['list_success'], $addresses);
    }

    /**
     * Delete User Address
     *
     * Deletes the user address specified by `id`.
     *
     * @bodyParam id int required The id of the address. Example: 1
     *
     * @responseFile 200 resources/responses/User/UserAddress/delete-user-address.json
     */
    public function delete(Request $req)
    {
        $req->validate([
            'id' => 'required|exists:user_addresses,id,user_id,' . Auth::user()->id,
        ]);

        if (UserAddress::find($req->id)->delete()) {
          return response()->success(__('user_address')['delete_success']);
        }
        return response()->error(__('user_address')['delete_error']);
    }

    /**
     * Update User Address
     *
     * Updates an address using the values provided.
     *
     * @bodyParam id int required The id of address being updated. Example: 1
     * @bodyParam name string The name on the address. Example: John
     * @bodyParam phone string The contact number. Example: 01643 214201
     * @bodyParam address_line_1 string First line of the address. Example: 21a Edgewood House
     * @bodyParam address_line_2 string Second line of the address. Example: Greenbank Estate
     * @bodyParam address_line_3 string Third line of the address. Example: Foxhayes
     * @bodyParam town string The town of the address. Example: Exeter
     * @bodyParam county string The town of the address. Example: Devon
     * @bodyParam postcode string The postcode of the address. Example EX16 5BH
     * @bodyParam country string The country of address. Example United Kingdom
     * @bodyParam delivery_instructions string Any additional instructions for deliveries to this address. Example: Leave parcels behind the shed.
     *
     * @responseFile 200 resources/responses/User/UserAddress/update-user-address.json
     */
    public function update(UpdateUserAddressRequest $req)
    {
        $address = UserAddress::find($req->id);

        $data = $req->validated();
        $data['is_default'] = Auth::user()->addresses()->exists() ? false : true;

        $address->fill($data);
        $address->save();

        return $address->wasChanged()
            ? response()->success(__('user_address')['update_success'], $address)
            : response()->success(__('nothing_updated'), $address);
    }
}






