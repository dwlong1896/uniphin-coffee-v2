<?php
$publicBase = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
$toUrl = static function (string $path) use ($publicBase): string {
    return ($publicBase === '' ? '' : $publicBase) . '/' . ltrim($path, '/');
};
?>

<div class="row">
    <div class="col-12 mt-5">
        <div class="card">
            <div class="card-body">
                <div class="d-sm-flex justify-content-between align-items-center">
                    <h4 class="header-title">Danh sach san pham</h4>
                    <button class="btn btn-primary mb-3" type="button">
                        <i class="fa-solid fa-plus"></i> Them san pham
                    </button>
                </div>
                <div class="data-tables">
                    <table id="dataTable" class="text-center">
                        <thead class="bg-light text-capitalize">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>P_Cate_ID</th>
                                <th>Description</th>
                                <th>Status</th>
                                <th>Price</th>
                                <th>Stock Quantity</th>
                                <th>Image</th>
                                <th>Slug</th>
                                <th>Updated At</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Tra sua matcha kem trung</td>
                                <td>3</td>
                                <td>Tra sua matcha ket hop kem trung beo min...</td>
                                <td>active</td>
                                <td>49000.00</td>
                                <td>120</td>
                                <td>matcha-kem-trung.png</td>
                                <td>tra-sua-matcha-kem-trung</td>
                                <td>2026-05-05 10:30:00</td>
                                <td>
                                    <a href="<?php echo $toUrl('admin/viewdetail'); ?>" class="text-primary mr-3"
                                        title="Xem chi tiet">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <a href="#" class="text-danger" title="Xoa">
                                        <i class="ti-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Ca phe sua da</td>
                                <td>2</td>
                                <td>Ca phe dam vi truyen thong, de uong moi ngay...</td>
                                <td>active</td>
                                <td>35000.00</td>
                                <td>85</td>
                                <td>cafe-sua-da.png</td>
                                <td>ca-phe-sua-da</td>
                                <td>2026-05-04 15:20:00</td>
                                <td>
                                    <a href="<?php echo $toUrl('admin/viewdetail'); ?>" class="text-primary mr-3"
                                        title="Xem chi tiet">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <a href="#" class="text-danger" title="Xoa">
                                        <i class="ti-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Tra dao cam sa</td>
                                <td>4</td>
                                <td>Thuc uong thanh mat voi huong dao va sa...</td>
                                <td>inactive</td>
                                <td>42000.00</td>
                                <td>40</td>
                                <td>tra-dao-cam-sa.png</td>
                                <td>tra-dao-cam-sa</td>
                                <td>2026-05-03 08:45:00</td>
                                <td>
                                    <a href="<?php echo $toUrl('admin/viewdetail'); ?>" class="text-primary mr-3"
                                        title="Xem chi tiet">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <a href="#" class="text-danger" title="Xoa">
                                        <i class="ti-trash"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>4</td>
                                <td>Banh tiramisu</td>
                                <td>5</td>
                                <td>Banh mem min, huong ca phe nhe va beo...</td>
                                <td>out_of_stock</td>
                                <td>38000.00</td>
                                <td>0</td>
                                <td>banh-tiramisu.png</td>
                                <td>banh-tiramisu</td>
                                <td>2026-05-02 12:10:00</td>
                                <td>
                                    <a href="<?php echo $toUrl('admin/viewdetail'); ?>" class="text-primary mr-3"
                                        title="Xem chi tiet">
                                        <i class="ti-eye"></i>
                                    </a>
                                    <a href="#" class="text-danger" title="Xoa">
                                        <i class="ti-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
