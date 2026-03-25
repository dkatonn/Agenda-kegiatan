@extends('admin.layout')

@section('title','Dashboard')

@section('content')

<!-- QUICK INFO -->
<div class="row mb-4">

    <div class="col-md-3">
        <div class="admin-card text-center">
            <h5>{{ $agenda->count() }}</h5>
            <small>Agenda</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card text-center">
            <h5>{{ $employee->count() }}</h5>
            <small>Pegawai</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card text-center">
            <h5>5</h5>
            <small>Video</small>
        </div>
    </div>

    <div class="col-md-3">
        <div class="admin-card text-center">
            <h5>{{ !empty($setting['running_text']) ? 'Aktif' : 'Kosong' }}</h5>
            <small>Running Text</small>
        </div>
    </div>

</div>


<div class="row">

    <!-- PREVIEW TV -->
    <div class="col-md-8">
        <div class="admin-card">

            <h6 class="card-title">
                <i class="bi bi-tv"></i>
                Preview TV
            </h6>

            <div class="preview-tv"
                style="
                    background-image: url('{{ isset($setting['background']) ? asset('storage/'.$setting['background']) : '' }}');
                    background-size: cover;
                    background-position: center;
                 ">

                @if(empty($setting['background']))
                <span class="preview-label">Preview Tampilan TV</span>
                @endif

                <div class="running-text-preview">
                    {{ $setting['running_text'] ?? 'Running text belum diisi' }}
                </div>

            </div>

        </div>
    </div>


    <!-- DISPLAY SETTINGS -->
    <div class="col-md-4">

        <div class="admin-card">

            <h6 class="card-title">
                <i class="bi bi-gear"></i>
                Display Settings
            </h6>

            <form action="{{ route('admin.setting.update') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- Background -->
                <div class="mb-3">
                    <label class="form-label">Background TV</label>
                    <input type="file" name="bg_image" class="form-control">
                </div>

                <hr>

                <!-- Running Text -->
                <div class="mb-3">
                    <label class="form-label">Running Text</label>
                    <input type="text"
                        name="running_text"
                        class="form-control"
                        value="{{ $setting['running_text'] ?? '' }}"
                        placeholder="Masukkan teks berjalan...">

                    <small class="text-muted">
                        Teks akan tampil berjalan di layar TV
                    </small>
                </div>

                <button class="btn btn-primary w-100">
                    Simpan
                </button>

            </form>

        </div>

    </div>

</div>


<!-- INFO -->
<div class="admin-card mt-4">
    <div class="info-box">
        Gunakan menu di samping untuk mengelola data pegawai, agenda, dan video.
    </div>
</div>

@endsection