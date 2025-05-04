@extends('profile.layout')

@section('title', 'Ubah Profil')

@section('breadcrumb')
<li>
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <a href="{{ route('profile.index') }}" class="ml-1 text-gray-500 md:ml-2 hover:text-blue-600">Profil</a>
    </div>
</li>
<li aria-current="page">
    <div class="flex items-center">
        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
        </svg>
        <span class="ml-1 text-gray-500 md:ml-2">Ubah</span>
    </div>
</li>
@endsection

@section('profile-content')
<form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
    @csrf
    @method('PUT')
    
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="md:col-span-2 flex flex-col items-center">
            <div class="w-24 h-24 rounded-full overflow-hidden mb-4 relative bg-blue-500 flex items-center justify-center">
                @if(Auth::user()->avatar)
                    <img id="avatar-preview" src="{{ asset('storage/' . Auth::user()->avatar) }}" 
                         alt="Foto Profil" class="w-full h-full object-cover">
                @else
                    <div id="avatar-preview" class="text-white text-3xl font-bold">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                @endif
            </div>
            
            <div class="mb-6">
                <div class="flex justify-center mb-4">
                    <label for="avatar"
                        class="cursor-pointer bg-blue-50 hover:bg-blue-100 text-blue-600 px-4 py-2 rounded-md text-sm">
                        <i class="fas fa-camera mr-1"></i> Ubah Foto
                    </label>
                    @if(Auth::user()->avatar)
                    <button type="button" class="cursor-pointer bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-md text-sm ml-2" onclick="confirmRemovePhoto()">
                        <i class="fas fa-trash mr-1"></i> Hapus Foto
                    </button>
                    @endif
                    <input type="hidden" id="remove_avatar" name="remove_avatar" value="0">
                    <input type="file" id="avatar" name="avatar" class="hidden" accept="image/*" onchange="previewImage()">
                </div>
                <p class="text-xs text-gray-500 mt-2">Ukuran file maksimal: 2MB. Format yang didukung: JPEG, PNG, JPG, GIF</p>
                @error('avatar')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
        </div>
        
        <div>
            <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
            <input type="text" name="name" id="name" value="{{ old('name', Auth::user()->name) }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('name')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Alamat Email</label>
            <input type="email" name="email" id="email" value="{{ old('email', Auth::user()->email) }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('email')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
        
        <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Nomor Telepon</label>
            <input type="text" name="phone" id="phone" value="{{ old('phone', Auth::user()->phone) }}" 
                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('phone')
                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
            @enderror
        </div>
    </div>
    
    <div class="border-t border-gray-200 mt-8 pt-8">
        <h3 class="text-lg font-medium mb-4">Ubah Kata Sandi</h3>
        <p class="text-sm text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>
        
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Saat Ini</label>
                <input type="password" name="current_password" id="current_password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('current_password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div></div>
            
            <div>
                <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Kata Sandi Baru</label>
                <input type="password" name="password" id="password" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Kata Sandi Baru</label>
                <input type="password" name="password_confirmation" id="password_confirmation" 
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>
    
    <div class="flex justify-end mt-8">
        <a href="{{ route('profile.index') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 mr-2">
            Batal
        </a>
        <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-md">
            Simpan Perubahan
        </button>
    </div>
</form>
@endsection

@section('scripts')
<script>
    // Fungsi preview gambar
    function previewImage() {
        const file = document.getElementById('avatar').files[0];
        if (file) {
            // Cek ukuran file
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (file.size > maxSize) {
                Swal.fire({
                    title: 'File Terlalu Besar!',
                    text: 'Maximum file size adalah 2MB. Silakan pilih file yang lebih kecil.',
                    icon: 'error',
                    timer: 3000,
                    showConfirmButton: false,
                });
                document.getElementById('avatar').value = '';
                return;
            }
            
            // Tampilkan preview
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('avatar-preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    }

    // Handler submit form
    document.querySelector('form').addEventListener('submit', function(e) {
        e.preventDefault();
        
        Swal.fire({
            title: 'Menyimpan perubahan...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        this.submit();
    });

    // Tampilkan notifikasi session
    @if(session('success'))
        Swal.fire({
            title: 'Berhasil!',
            text: '{{ session('success') }}',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    @endif

    @if(session('error'))
        Swal.fire({
            title: 'Gagal!',
            text: '{{ session('error') }}',
            icon: 'error',
            confirmButtonText: 'OK',
            confirmButtonColor: '#3085d6'
        });
    @endif

    function confirmRemovePhoto() {
        Swal.fire({
            title: 'Hapus Foto Profil?',
            text: 'Foto profil Anda akan dihapus dan diganti dengan foto default',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('avatar-preview').src='{{ asset('img/default-avatar.png') }}';
                document.getElementById('avatar').value='';
                document.getElementById('remove_avatar').value='1';
                // Show confirmation that photo will be removed on save
                Swal.fire({
                    title: 'Foto Akan Dihapus',
                    text: 'Perubahan akan diterapkan setelah Anda menyimpan profil',
                    icon: 'info',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            }
        });
    }
</script>
@endsection
