<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Actions\Customer\CreateCustomerAction;
use App\Actions\Customer\GetCustomersAction;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Resources\CustomerResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use App\Actions\Customer\UpdateCustomerAction;
use App\Actions\Customer\DeleteCustomerAction;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;

class CustomerController extends Controller
{
    /**
     * Müşteri listesini filtreli ve sayfalanmış (paginated) olarak döndürür.
     */
    public function index(Request $request, GetCustomersAction $action): AnonymousResourceCollection
    {
        // Yetki Kontrolü (Gate::before üzerinden Developer Bypass destekli)
        // Controller seviyesinde hızlı yetki kontrolü için
        // \Gate::authorize('customers.view'); // Yetki slug'ı eklendiğinde açılabilir

        // Gelen sadece izin verilen filtreleri al
        $filters = $request->only(['status']);
        $perPage = $request->integer('per_page', 15);

        // Action sınıfından paginated veriyi çek
        $customers = $action->execute($filters, $perPage);

        // Laravel Resource Collection ile sarmala. Pagination metadata'sı (current_page, total vb.) otomatik eklenecek.
        return CustomerResource::collection($customers)->additional([
            'success' => true,
            'message' => 'Müşteriler başarıyla listelendi.',
        ]);
    }

    /**
     * Yeni müşteri kaydını alır, işler ve JSON olarak döndürür.
     */
    public function store(StoreCustomerRequest $request, CreateCustomerAction $action): JsonResponse
    {
        $validatedData = $request->validated();
        $customer = $action->execute($validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Müşteri kaydı başarıyla oluşturuldu.',
            // Tekil veriyi de API standartına oturtmak için Resource kullanıyoruz
            'data' => new CustomerResource($customer), 
        ], 201);
    }
    /**
     * Belirtilen müşteri kaydını günceller.
     */
    public function update(UpdateCustomerRequest $request, Customer $customer, UpdateCustomerAction $action): JsonResponse
    {
        $validatedData = $request->validated();
        
        $updatedCustomer = $action->execute($customer, $validatedData);

        return response()->json([
            'success' => true,
            'message' => 'Müşteri kaydı başarıyla güncellendi.',
            'data' => new CustomerResource($updatedCustomer),
        ]);
    }

    /**
     * Belirtilen müşteri kaydını sistemden siler (Soft Delete).
     */
    public function destroy(Customer $customer, DeleteCustomerAction $action): JsonResponse
    {
        // Yetki Kontrolü
        Gate::authorize('customers.delete');

        $action->execute($customer);

        return response()->json([
            'success' => true,
            'message' => 'Müşteri kaydı başarıyla silindi.',
        ]);
    }
}