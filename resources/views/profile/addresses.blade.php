@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0">Alamat Pengiriman</h2>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-12">
            <a href="{{ route('addresses.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Alamat Baru
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if($addresses->isEmpty())
        <div class="row">
            <div class="col-md-12">
                <div class="alert alert-info" role="alert">
                    <i class="bi bi-info-circle"></i> Anda belum memiliki alamat pengiriman. Silakan tambahkan alamat baru.
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach($addresses as $address)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm {{ $address->is_default ? 'border-primary' : '' }}">
                        <div class="card-body">
                            <h5 class="card-title d-flex justify-content-between">
                                <span>{{ $address->name }}</span>
                                @if($address->is_default)
                                    <span class="badge bg-primary">Utama</span>
                                @endif
                            </h5>
                            <p class="card-text mb-1">
                                <i class="bi bi-telephone"></i> {{ $address->phone }}
                            </p>
                            <p class="card-text">
                                <i class="bi bi-geo-alt"></i> 
                                {{ $address->address_line1 }}
                                @if($address->address_line2)
                                    <br><span class="ms-4">{{ $address->address_line2 }}</span>
                                @endif
                                <br><span class="ms-4">{{ $address->district }}, {{ $address->city }}</span>
                                <br><span class="ms-4">{{ $address->province }}, {{ $address->postal_code }}</span>
                            </p>
                            @if($address->notes)
                                <p class="card-text">
                                    <i class="bi bi-info-circle"></i> <small class="text-muted">{{ $address->notes }}</small>
                                </p>
                            @endif
                        </div>
                        <div class="card-footer bg-transparent">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <a href="{{ route('addresses.edit', $address->id) }}" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    
                                    @if(!$address->is_default)
                                        <form method="POST" action="{{ route('addresses.set-default', $address->id) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-star"></i> Jadikan Utama
                                            </button>
                                        </form>
                                    @endif
                                </div>
                                
                                @if($addresses->count() > 1)
                                    <form method="POST" action="{{ route('addresses.destroy', $address->id) }}" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus alamat ini?')">
                                            <i class="bi bi-trash"></i> Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<style>
    .card.border-primary {
        border-width: 2px;
    }
</style>
@endpush