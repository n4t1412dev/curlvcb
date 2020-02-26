<?php
/**
 *  Copyright (C) k1d1412, Inc - All Rights Reserved
 *  @author      k1d1412 <me@1412.io>
 *  @site        https://1412.dev
 *  @date        2/26/20, 9:41 AM
 */
date_default_timezone_set('Asia/Ho_Chi_Minh');
class VCBFunction
{
    private function curl($url)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        $headers[] = "Connection: keep-alive";
        $headers[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $headers[] = "Accept-Language: en-us,en;q=0.5";
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookievcb.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookievcb.txt');
        $page = curl_exec($ch);
        curl_close($ch);
        return $page;
    }

    private function post($url, $data)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
        curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');
        $headers = array();
        $headers[] = 'Connection: keep-alive';
        $headers[] = 'Accept: */*';
        $headers[] = 'Sec-Fetch-Dest: empty';
        $headers[] = 'X-Requested-With: XMLHttpRequest';
        $headers[] = 'User-Agent: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/80.0.3987.116 Safari/537.36';
        $headers[] = 'Content-Type: application/x-www-form-urlencoded; charset=UTF-8';
        $headers[] = 'Sec-Fetch-Site: same-origin';
        $headers[] = 'Sec-Fetch-Mode: cors';
        $headers[] = 'Accept-Language: vi-VN,vi;q=0.9,fr-FR;q=0.8,fr;q=0.7,en-US;q=0.6,en;q=0.5,pl;q=0.4';
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIEFILE,'cookievcb.txt');
        curl_setopt($ch, CURLOPT_COOKIEJAR, 'cookievcb.txt');

        $result = curl_exec($ch);
        if (curl_errno($ch)) {
            return -1;
        }
        curl_close ($ch);
        return $result;
    }

    private function get_string($begin, $end, $data)
    {
        $data = explode($begin,$data);
        $data = explode($end,$data[1]);
        return $data[0];
    }

    private function save_file($value)
    {
        $saveFile = fopen('1.jpg', 'w+');
        fwrite($saveFile, $value);
        fclose($saveFile);
    }

    public function getLogs($username, $password)
    {
        //get html cua ibanking2020
        $get_html = $this->curl('https://www.vietcombank.com.vn/ibanking2020/');
        //kiem tra neu khong co logout thi login
        if (!preg_match("/logout/", $get_html))
        {
            //get session để sử dụng
            $get_session = $this->get_string('<a href=/ibanking2020/', '/account/resetpassword', $get_html);
            //get url captcha để giải
            $get_captcha_url = $this->get_string('<img id=captchaImage style=margin-right:6px src="', '"', $get_html);
            $captcha_url = $this->curl('https://www.vietcombank.com.vn/'.$get_captcha_url);
            //save lại img của captcha
            $this->save_file($captcha_url);
            //get giải captcha từ sever khác thay link bằng site của bạn
            $bypass_captcha = json_decode(file_get_contents('https://lolusernamechecker.ga/captcha/?link=http://reportads.com/1.jpg?time='.time()));
            // nếu giải captcha thành công sẽ trả về status = ok
            if ($bypass_captcha->status == 'ok')
            {
                //get key captcha để post login
                $key_captcha = $this->get_string('guid=', ' ', $get_captcha_url);
                //thực hiện post login
                $login = $this->post("https://www.vietcombank.com.vn/IBanking2020/".$get_session."/Account/Login", "source=&username=".$username."&pass=".$password."&captcha=".$bypass_captcha->result."&captcha-guid1=".$key_captcha."");
                // nếu không có logout. ( do sai captcha hoặc sai user & pass)
                if (!preg_match('/logout/', $login))
                {
                    return json_encode(
                        array(
                            'status' => 'error',
                            'msg' => 'Lỗi không xác định. Vui lòng thử lại !!!'
                        )
                    );
                }
            }
            // giải captcha lỗi
            else
            {
                return json_encode(
                    array(
                        'status' => 'error',
                        'msg' => 'Sai captcha. Vui lòng thử lại !!!'
                    )
                );
            }
        }
        else
        {
            //get session để sử dụng
            $get_session = $this->get_string("var hashPath='", "'", $get_html);
            //get token request
            $get_tokenRequest = $this->get_string('name=__RequestVerificationToken type=hidden value=', '>', $get_html);
            $get_chiTietTaiKhoan = $this->curl('https://www.vietcombank.com.vn/ibanking2020/'.$get_session.'/thongtintaikhoan/taikhoan/chitiettaikhoan');
            $get_taiKhoanTrichNo = $this->get_string('id=TaiKhoanTrichNo data-mini=true><option value=', '>', $get_chiTietTaiKhoan);
            //thời gian kết thúc get logs giao dich
            $start_time = date('d/m/Y');
            //thời gian bắt đầu get logs giao dịch
            $end_time =  date("d/m/Y", strtotime("1 days ago"));
            $get_maLoaiTaiKhoanEncrypt = $this->get_string('|', ' ', $get_taiKhoanTrichNo);
            $get_AID = $this->get_string('id=TaiKhoanTrichNo data-mini=true><option value=', '|', $get_chiTietTaiKhoan);
            $get_thongTinChiTiet = json_decode($this->post('https://www.vietcombank.com.vn/IBanking2020/'.$get_session.'/ThongTinTaiKhoan/TaiKhoan/GetThongTinChiTiet', "ToKenData=&__RequestVerificationToken=".$get_tokenRequest."&TaiKhoanTrichNo=".urlencode($get_taiKhoanTrichNo)."&MaLoaiTaiKhoanEncrypt=".$get_maLoaiTaiKhoanEncrypt."SoDuHienTai=&LoaiTaiKhoan=&LoaiTienTe=&AID=".$get_AID."&NgayBatDauText=&NgayKetThucText="));
            $maLoaiTaiKhoan =  $get_thongTinChiTiet->DanhSachTaiKhoan[0]->MaLoaiTaiKhoan;
            $loaiTienTe = $get_thongTinChiTiet->DanhSachTaiKhoan[0]->LoaiTienTe;
            $soDuKhaDung = $get_thongTinChiTiet->DanhSachTaiKhoan[0]->SoDuKhaDung;
            $tokenData = $get_thongTinChiTiet->TokenData;
            $get_chiTietGiaoDich = json_decode($this->post('https://www.vietcombank.com.vn/IBanking2020/'.$get_session.'/ThongTinTaiKhoan/TaiKhoan/ChiTietGiaoDich', "ToKenData=".$tokenData."&__RequestVerificationToken=".$get_tokenRequest."&TaiKhoanTrichNo=".urlencode($get_taiKhoanTrichNo)."&MaLoaiTaiKhoanEncrypt=".$get_maLoaiTaiKhoanEncrypt."&SoDuHienTai=".$soDuKhaDung."&LoaiTaiKhoan=".$maLoaiTaiKhoan."&LoaiTienTe=".$loaiTienTe."&AID=".$get_AID."&NgayBatDauText=".urlencode($end_time)."&NgayKetThucText=".urlencode($start_time)));

            $ThongTinTaiKhoan = array(
                'SoDuDauKy' => $get_chiTietGiaoDich->SoDuDauKy.' VND',
                'SoDuCuoiKy' => $get_chiTietGiaoDich->SoDuCuoiKy.' VND',
                'NgayBatDau' => $get_chiTietGiaoDich->NgayBatDau,
                'NgayKetThuc'=> $get_chiTietGiaoDich->NgayKetThuc
            );
            $ChiTietGiaoDich = array();
            for ($i = 0; $i < count($get_chiTietGiaoDich->ChiTietGiaoDich); $i++)
            {
                $soThamChieu = $get_chiTietGiaoDich->ChiTietGiaoDich[$i]->SoThamChieu;
                $moTa = $get_chiTietGiaoDich->ChiTietGiaoDich[$i]->MoTa;
                $soTienChuyen = $get_chiTietGiaoDich->ChiTietGiaoDich[$i]->SoTienGhiNo;
                $soTienNhan = $get_chiTietGiaoDich->ChiTietGiaoDich[$i]->SoTienGhiCo;
                $ngayGiaoDich = $get_chiTietGiaoDich->ChiTietGiaoDich[$i]->NgayGiaoDich;
                if ($soTienChuyen != "-")
                {
                    $ChiTietGiaoDich[] = array(
                        'NgayGiaoDich' =>  $ngayGiaoDich,
                        'ThayDoi' => '-',
                        'MaThamChieu' => $soThamChieu,
                        'MoTa' => $moTa,
                        'SoTien' => $soTienChuyen.' VND'

                    );
                }
                if ($soTienNhan != "-")
                {
                    $ChiTietGiaoDich[] = array(
                        'NgayGiaoDich' =>  $ngayGiaoDich,
                        'ThayDoi' => '+',
                        'MaThamChieu' => $soThamChieu,
                        'MoTa' => $moTa,
                        'SoTien' => $soTienNhan.' VND'

                    );
                }

            }
            $data = array(
                'ChiTietGiaoDich' => $ChiTietGiaoDich,
                'ThongTinTaiKhoan' => $ThongTinTaiKhoan
            );
            return json_encode(
                array(
                    'status' => 'success',
                    'msg' => 'Get thành công thông tin giao dịch',
                    'data' => $data
                )
            );
        }
    }
}