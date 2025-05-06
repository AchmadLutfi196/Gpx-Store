@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2>{{ isset($address) ? 'Edit Alamat' : 'Tambah Alamat Baru' }}</h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="{{ isset($address) ? route('addresses.update', $address->id) : route('addresses.store') }}">
                        @csrf
                        @if(isset($address))
                            @method('PUT')
                        @endif

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label">Nama Penerima <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror " id="name" name="name" 
                                       value="{{ $address->name ?? old('name') }}" required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="phone" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" name="phone" 
                                       value="{{ $address->phone ?? old('phone') }}" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address_line1" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('address_line1') is-invalid @enderror" id="address_line1" 
                                   name="address_line1" value="{{ $address->address_line1 ?? old('address_line1') }}" required>
                            @error('address_line1')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="address_line2" class="form-label">Alamat Tambahan (Opsional)</label>
                            <input type="text" class="form-control @error('address_line2') is-invalid @enderror" id="address_line2" 
                                   name="address_line2" value="{{ $address->address_line2 ?? old('address_line2') }}">
                            @error('address_line2')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="province" class="form-label">Provinsi <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('province') is-invalid @enderror" id="province" 
                                       name="province" value="{{ $address->province ?? old('province') }}" required>
                                @error('province')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="city" class="form-label">Kota/Kabupaten <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('city') is-invalid @enderror" id="city" 
                                       name="city" value="{{ $address->city ?? old('city') }}" required>
                                @error('city')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="district" class="form-label">Kecamatan <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" id="district" 
                                       name="district" value="{{ $address->district ?? old('district') }}" required>
                                @error('district')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="postal_code" class="form-label">Kode Pos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('postal_code') is-invalid @enderror" id="postal_code" 
                                       name="postal_code" value="{{ $address->postal_code ?? old('postal_code') }}" required>
                                @error('postal_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="notes" class="form-label">Catatan (Opsional)</label>
                            <textarea class="form-control @error('notes') is-invalid @enderror" id="notes" name="notes" 
                                      rows="3">{{ $address->notes ?? old('notes') }}</textarea>
                            <div class="form-text">Contoh: Warna pagar, patokan, dll.</div>
                            @error('notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="is_default" name="is_default" 
                                       {{ (isset($address) && $address->is_default) || old('is_default') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_default">
                                    Jadikan alamat utama
                                </label>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i>
                                {{ isset($address) ? 'Perbarui Alamat' : 'Simpan Alamat' }}
                            </button>
                            <a href="{{ route('addresses.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle"></i> Batal
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card shadow-sm bg-light">
                <div class="card-body">
                    <h5 class="card-title">Informasi</h5>
                    <p class="card-text">
                        <i class="bi bi-info-circle"></i> Pastikan alamat yang Anda masukkan sudah benar dan lengkap untuk memudahkan pengiriman.
                    </p>
                    <p class="card-text">
                        <i class="bi bi-star"></i> Anda dapat menjadikan alamat sebagai alamat utama yang akan digunakan sebagai default saat checkout.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
@endpush