<?php
/**
 *  Copyright (C) k1d1412, Inc - All Rights Reserved
 *  @author      k1d1412 <me@1412.io>
 *  @site        https://1412.dev
 *  @date        2/26/20, 7:27 PM
 */
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Vietcombank Logs</title>

    <!-- Custom fonts for this template -->
    <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
    <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/sb-admin-2.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">
    <!-- Custom styles for this page -->
    <link href="vendor/datatables/dataTables.bootstrap4.min.css" rel="stylesheet">

</head>

<body id="page-top">

<!-- Page Wrapper -->
<div id="wrapper">


    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Begin Page Content -->
            <div class="container-fluid">
                <br>
                <div class="card shadow mb-4" id="login">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Vietcombank Logs</h6>
                    </div>
                    <div class="card-body">
                        <form id="getlogs" method="post">
                            <div class="form-group">
                                <label>Username</label>
                                <input type="text"class="form-control" id="username" name="username">
                            </div>
                            <div class="form-group">
                                <label>Password</label>
                                <input type="password" class="form-control" id="password" name="password">
                            </div>

                        </form>
                        <button type="submit" id="submit" class="btn btn-primary">GET</button>
                    </div>
                </div>
                <!-- DataTales Example -->
                <div class="card shadow mb-4" id="table_logs" style="display: none">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Logs</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                <tr>
                                    <th>Ngày giao dịch</th>
                                    <th>Số tham chiếu</th>
                                    <th>Thay đổi</th>
                                    <th>Số tiền</th>
                                    <th>Mô tả</th>
                                </tr>
                                </thead>
                                <tbody id="logs">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->

        </div>
        <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->

</div>
<!-- End of Page Wrapper -->

<!-- Bootstrap core JavaScript-->
<script src="vendor/jquery/jquery.min.js"></script>
<script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

<!-- Core plugin JavaScript-->
<script src="vendor/jquery-easing/jquery.easing.min.js"></script>

<!-- Custom scripts for all pages-->
<script src="js/sb-admin-2.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<!-- Page level custom scripts -->
<script>
    toastr.options = {
        "closeButton": true,
        "debug": false,
        "newestOnTop": false,
        "progressBar": false,
        "positionClass": "toast-top-right",
        "preventDuplicates": false,
        "onclick": null,
        "showDuration": "300",
        "hideDuration": "1000",
        "timeOut": "2000",
        "extendedTimeOut": "1000",
        "showEasing": "swing",
        "hideEasing": "linear",
        "showMethod": "fadeIn",
        "hideMethod": "fadeOut"
    }
    $(document).ready(function(){
        $("#submit").click(function(){
            var http = new XMLHttpRequest();
            var data = "username=" + $('#username').val() + '&password=' + $('#password').val();
            http.onreadystatechange = function() {
                if (this.readyState == 4 && this.status == 200) {
                    var result = JSON.parse(this.responseText);
                    if (result.status == 'error')
                    {
                        toastr["error"](result.msg);
                        return;
                    }
                    if (result.status == 'success')
                    {
                        toastr["success"](result.msg);
                        $('#table_logs').show();
                        $('#login').hide();
                        var data_logs = [];
                        result.data.ChiTietGiaoDich.forEach(function (item) {
                            data_logs.push([item.NgayGiaoDich, item.MaThamChieu, item.ThayDoi, item.SoTien, item.MoTa]);
                        });
                        $('#dataTable').DataTable({
                            data: data_logs
                        });
                    }
                }
            }

            http.open('GET', 'getlogs.php?'+data, true);
            http.send();
        });
    });
</script>
</body>

</html>