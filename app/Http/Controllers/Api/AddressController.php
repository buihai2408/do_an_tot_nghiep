<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Address\StoreAddressRequest;
use App\Http\Requests\Address\UpdateAddressRequest;
use App\Models\Address;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->addresses()->orderByDesc('is_default')->get());
    }

    public function store(StoreAddressRequest $request)
    {
        $data = $request->validated();

        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->update(['is_default' => false]);
        }

        $address = Auth::user()->addresses()->create($data);
        return response()->json(['message' => 'Đã thêm địa chỉ!', 'address' => $address], 201);
    }

    public function update(UpdateAddressRequest $request, Address $address)
    {
        if ($address->user_id !== Auth::id()) abort(403);

        $data = $request->validated();
        if (!empty($data['is_default'])) {
            Auth::user()->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
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
