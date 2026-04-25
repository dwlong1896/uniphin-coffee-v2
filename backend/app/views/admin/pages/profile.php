<?php

$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');

// helper asset
$assetUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/assets/' . ltrim($path, '/');
};

// helper route/url
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};

$fullName  = htmlspecialchars(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''), ENT_QUOTES, 'UTF-8');
$firstName = htmlspecialchars($user['first_name'] ?? '', ENT_QUOTES, 'UTF-8');
$lastName  = htmlspecialchars($user['last_name']  ?? '', ENT_QUOTES, 'UTF-8');
$email     = htmlspecialchars($user['email']      ?? '', ENT_QUOTES, 'UTF-8');
$role      = htmlspecialchars($user['role']       ?? '', ENT_QUOTES, 'UTF-8');
$phone     = htmlspecialchars($user['phone']      ?? '', ENT_QUOTES, 'UTF-8');
$address   = htmlspecialchars($user['address']    ?? '', ENT_QUOTES, 'UTF-8');
$gender    = htmlspecialchars($user['gender']     ?? '', ENT_QUOTES, 'UTF-8');
$birthDate = htmlspecialchars($user['birth_date'] ?? '', ENT_QUOTES, 'UTF-8');

// 🔥 Avatar
$avatar = !empty($user['image'])
    ? $toUrl('uploads/' . $user['image'])
    : $toUrl('images/default-avatar.png');
?>

<!-- profile header card -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-body text-center text-white p-4"
                style="background: linear-gradient(135deg, #8914fe, #8063f5); border-radius: 0.375rem;">
                <div class="mb-3">
                    <img src="<?php echo $avatar; ?>" alt="avatar"
                        style="width:100px;height:100px;border-radius:50%;border:3px solid rgba(255,255,255,0.3);object-fit:cover;" />
                </div>
                <h3 class="mb-1"><?php echo $fullName; ?></h3>
                <p class="mb-3" style="opacity:0.85"><?php echo $role; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- two-column layout -->
<div class="row mt-4">

    <!-- about me -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">About Me</h4>
            </div>
            <div class="card-body">
                <ul class="list-group list-group-flush">
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-user me-2 text-muted"></i> Full Name</span>
                        <strong><?php echo $fullName; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-envelope me-2 text-muted"></i> Email</span>
                        <strong><?php echo $email; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-phone me-2 text-muted"></i> Phone</span>
                        <strong><?php echo $phone ?: '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-location-dot me-2 text-muted"></i> Address</span>
                        <strong><?php echo $address ?: '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-person me-2 text-muted"></i> Gender</span>
                        <strong><?php echo $gender ?: '—'; ?></strong>
                    </li>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span><i class="fa-solid fa-calendar-days me-2 text-muted"></i> Birth Date</span>
                        <strong><?php echo $birthDate ?: '—'; ?></strong>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- edit profile -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h4 class="header-title mb-0">Edit Profile</h4>
            </div>
            <div class="card-body">

                <form action="<?php echo $toUrl('admin/profile'); ?>" method="post" enctype="multipart/form-data">

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">First Name</label>
                            <input type="text" name="first_name" class="form-control"
                                value="<?php echo $firstName; ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Last Name</label>
                            <input type="text" name="last_name" class="form-control" value="<?php echo $lastName; ?>" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" value="<?php echo $email; ?>" />
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="tel" name="phone" class="form-control" value="<?php echo $phone; ?>" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?php echo $address; ?>" />
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select name="gender" class="form-control">
                                <option value="">-- Select --</option>
                                <option value="male" <?php echo $gender === 'male' ? 'selected' : ''; ?>>Male</option>
                                <option value="female" <?php echo $gender === 'female' ? 'selected' : ''; ?>>Female
                                </option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Birth Date</label>
                            <input type="date" name="birth_date" class="form-control"
                                value="<?php echo $birthDate; ?>" />
                        </div>
                    </div>


                    <div class="row mb-3">
                        <div class="col-12">
                            <label class="form-label">Avatar</label>
                            <input type="file" name="avatar" class="form-control">
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>

</div>