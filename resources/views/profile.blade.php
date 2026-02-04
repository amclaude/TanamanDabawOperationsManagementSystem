@extends('layouts.app')

@section('title', 'Profile | Tanaman')

@push('styles')
<style>
    /* Card Layout */
    .profile-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .profile-banner {
        height: 140px;
        background: linear-gradient(135deg, #1A4D3E 0%, #319B72 100%);
    }

    .profile-body {
        padding: 2rem;
    }

    /* Header & Avatar */
    .profile-header-row {
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        margin-top: -70px;
        margin-bottom: 2.5rem;
    }

    .profile-avatar-wrapper {
        position: relative;
        display: inline-block;
    }

    .profile-avatar-large {
        width: 130px;
        height: 130px;
        border-radius: 50%;
        object-fit: cover;
        border: 5px solid #fff;
        background: #fff;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .camera-btn {
        position: absolute;
        bottom: 8px;
        right: 8px;
        background: #1A4D3E;
        color: white;
        width: 34px;
        height: 34px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 3px solid #fff;
        transition: all 0.2s;
    }

    .camera-btn:hover {
        background: #319B72;
        transform: scale(1.1);
    }

    /* Form Grid */
    .profile-form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .input-group {
        display: flex;
        flex-direction: column;
        gap: 0.6rem;
    }

    .input-group.full-width {
        grid-column: span 2;
    }

    .input-group label {
        font-size: 0.75rem;
        font-weight: 700;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    /* Input with Icons Fix */
    .input-with-icon {
        position: relative;
        width: 100%;
    }

    .input-with-icon i {
        position: absolute;
        left: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        z-index: 10;
        pointer-events: none;
    }

    .input-with-icon input,
    .input-with-icon textarea {
        width: 100%;
        padding: 12px 15px 12px 45px;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        font-size: 0.95rem;
        color: #334155;
        background-color: #f8fafc;
        transition: all 0.2s ease;
        outline: none;
    }

    /* Textarea doesn't use the icon padding */
    .input-with-icon textarea {
        padding-left: 15px;
        resize: vertical;
    }

    .input-with-icon input:focus,
    .input-with-icon textarea:focus {
        border-color: #319B72;
        background-color: #fff;
        box-shadow: 0 0 0 3px rgba(49, 155, 114, 0.1);
    }

    .input-with-icon input[readonly],
    .input-with-icon textarea[readonly] {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        cursor: default;
        box-shadow: none;
    }

    /* Buttons */
    .btn-primary {
        padding: 12px 24px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.2s;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-save {
        background-color: #1A4D3E;
        color: white;
    }

    .btn-save:hover {
        background-color: #143a2f;
    }

    .btn-edit {
        background-color: #f1f5f9;
        color: #334155;
        border: 1px solid #cbd5e1;
    }

    .btn-edit:hover {
        background-color: #e2e8f0;
    }

    /* Error Text */
    .error-text {
        color: #ef4444;
        font-size: 0.75rem;
        font-weight: 500;
        margin-top: 2px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div>
        <h2>My Profile</h2>
        <p>Manage your personal information and security</p>
    </div>
</div>

<div id="session-data"
    data-profile-status="{{ session('status') === 'profile-updated' ? 'true' : 'false' }}"
    data-password-status="{{ session('status') === 'password-updated' ? 'true' : 'false' }}">
</div>

<div class="profile-card">
    <div class="profile-banner"></div>
    <div class="profile-body">
        <div class="profile-header-row">
            <div class="profile-avatar-wrapper">
                <img src="{{ asset('images/images.jpg') }}" alt="{{ $user->name }}" class="profile-avatar-large">
                <div class="camera-btn"><i class="fas fa-camera"></i></div>
            </div>
            <button type="button" class="btn-primary" id="editProfileBtn" style="background-color: #f1f5f9; color: #334155; border: 1px solid #cbd5e1;">
                Edit Profile
            </button>
        </div>

        <form action="{{ route('profile.update') }}" method="POST" class="profile-form-grid" id="profileForm">
            @csrf
            @method('PATCH')
            <div class="input-group">
                <label>FULL NAME</label>
                <div class="input-with-icon">
                    <i class="far fa-user"></i>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" readonly>
                </div>
                @error('name') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label>EMAIL ADDRESS</label>
                <div class="input-with-icon">
                    <i class="far fa-envelope"></i>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" readonly>
                </div>
                @error('email') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label>PHONE</label>
                <div class="input-with-icon">
                    <i class="fas fa-phone-alt"></i>
                    <input type="tel" name="phone" value="{{ old('phone', $user->phone) }}" readonly>
                </div>
            </div>

            <div class="input-group">
                <label>LOCATION</label>
                <div class="input-with-icon">
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" name="location" value="{{ old('location', $user->location) }}" readonly>
                </div>
            </div>

            <div class="input-group full-width">
                <label>BIO</label>
                <div class="input-with-icon" style="padding:0">
                    <textarea name="bio" rows="4" readonly style="padding-left: 15px;">{{ old('bio', $user->bio) }}</textarea>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="profile-card" style="margin-top: 2rem;">
    <div class="profile-body">
        <div style="margin-bottom: 1.5rem;">
            <h3 style="color: #1e293b; margin-bottom: 0.5rem;">Update Password</h3>
            <p style="color: #64748b; font-size: 0.9rem;">Ensure your account is using a long, random password to stay secure.</p>
        </div>

        <form action="{{ route('profile.password') }}" method="POST" class="profile-form-grid">
            @csrf
            @method('PUT')
            <div class="input-group full-width">
                <label>CURRENT PASSWORD</label>
                <div class="input-with-icon">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="current_password" required style="background-color: #fff;">
                </div>
                @error('current_password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label>NEW PASSWORD</label>
                <div class="input-with-icon">
                    <i class="fas fa-key"></i>
                    <input type="password" name="password" required style="background-color: #fff;">
                </div>
                @error('password') <span class="error-text">{{ $message }}</span> @enderror
            </div>

            <div class="input-group">
                <label>CONFIRM PASSWORD</label>
                <div class="input-with-icon">
                    <i class="fas fa-check-double"></i>
                    <input type="password" name="password_confirmation" required style="background-color: #fff;">
                </div>
            </div>

            <div class="full-width" style="display: flex; justify-content: flex-end; margin-top: 1rem;">
                <button type="submit" class="btn-primary" style="background-color: #1A4D3E; color: white; border: none;">
                    Update Password
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const editBtn = document.getElementById('editProfileBtn');
        const profileForm = document.getElementById('profileForm');
        const formInputs = document.querySelectorAll('#profileForm input, #profileForm textarea');
        const sessionData = document.getElementById('session-data');

        let isEditing = false;

        if (editBtn) {
            editBtn.addEventListener('click', () => {
                if (!isEditing) {
                    isEditing = true;
                    formInputs.forEach(input => {
                        input.removeAttribute('readonly');
                        input.style.backgroundColor = "#fff";
                        input.style.borderColor = "#319B72";
                    });
                    editBtn.textContent = "Save Changes";
                    editBtn.style.backgroundColor = "#1A4D3E";
                    editBtn.style.color = "white";
                    formInputs[0].focus();
                } else {
                    profileForm.submit();
                }
            });
        }

        const profileUpdated = sessionData.dataset.profileStatus === 'true';
        const passwordUpdated = sessionData.dataset.passwordStatus === 'true';

        if (profileUpdated || passwordUpdated) {
            Swal.fire({
                title: profileUpdated ? 'Profile Updated!' : 'Password Changed!',
                text: profileUpdated ? 'Your information has been saved.' : 'Security updated.',
                icon: 'success',
                confirmButtonColor: '#319B72',
                timer: 2000,
                showConfirmButton: false
            });
        }
    });
</script>
@endpush