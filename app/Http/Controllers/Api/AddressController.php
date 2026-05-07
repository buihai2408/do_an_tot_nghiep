<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    protected function currentUser(): User
    {
        $user = Auth::user();
        if (!$user instanceof User) {
            abort(401);
        }

        return $user;
    }

    public function index()
    {
        return response()->json($this->currentUser()->addresses()->orderByDesc('is_default')->get());
    }

    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            $this->currentUser()->addresses()->update(['is_default' => false]);
        }

        $address = $this->currentUser()->addresses()->create($data);
        return response()->json(['message' => 'Đã thêm địa chỉ!', 'address' => $address], 201);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $data = $request->validated();
        if (!empty($data['is_default'])) {
            $this->currentUser()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
        }

        $address->update($data);
        return response()->json(['message' => 'Đã cập nhật địa chỉ!', 'address' => $address]);
    }

    public function destroy(Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);
        $address->delete();
        return response()->json(['message' => 'Đã xóa địa chỉ!']);
    }
}
