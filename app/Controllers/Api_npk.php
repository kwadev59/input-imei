<?php
namespace App\Controllers;

class Api_npk extends BaseController
{
    /**
     * Endpoint untuk mengambil data NPK dari API eksternal
     * 
     * URL: /api_npk/get_npk
     */
    public function get_npk()
    {
        // Path temporary untuk menyimpan cookie session (ci_session)
        $cookie_file = sys_get_temp_dir() . '/cookie_simad2.txt';

        /**
         * STEP 1: LOGIN
         * Melakukan login terlebih dahulu agar mendapatkan session aktif
         */
        $login_url = 'https://simad2.astra-agro.co.id/login';
        
        $ch_login = curl_init($login_url);
        curl_setopt($ch_login, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_login, CURLOPT_POST, true);
        curl_setopt($ch_login, CURLOPT_POSTFIELDS, http_build_query([
            'username' => 'afirmansyah',
            'password' => 'andilee9'
        ]));
        // Simpan cookie hasil set dari server ke file
        curl_setopt($ch_login, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch_login, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_login, CURLOPT_SSL_VERIFYHOST, false);
        // Follow redirect setelah login berhasil (opsional)
        curl_setopt($ch_login, CURLOPT_FOLLOWLOCATION, true);
        
        curl_exec($ch_login);
        
        if (curl_errno($ch_login)) {
             $error_msg = curl_error($ch_login);
             curl_close($ch_login);
             return $this->response->setStatusCode(500)->setJSON([
                 'status' => 'error',
                 'message' => 'Gagal auto-login: ' . $error_msg
             ]);
        }
        curl_close($ch_login);

        /**
         * STEP 2: FETCH DATA
         * Request data NPK menggunakan cookie session dari tahap login
         */
        $api_url = 'https://simad2.astra-agro.co.id/master/lov/list_npk/BIM';

        $headers = [
            'X-Requested-With: XMLHttpRequest',
            'Origin: https://simad2.astra-agro.co.id',
            'Referer: https://simad2.astra-agro.co.id/site/assign_pengguna'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        // Pakai cookie yang tadi disave dari login
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            $error_msg = curl_error($ch);
            curl_close($ch);
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'cURL Error saat fetch data: ' . $error_msg
            ]);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        // Bersihkan temp cookie file (opsional paska pakai)
        if (file_exists($cookie_file)) {
            @unlink($cookie_file);
        }

        // Decode JSON output
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
             return $this->response->setStatusCode(500)->setJSON([
                 'status' => 'error',
                 'message' => 'Gagal parsing JSON dari API. Server membalas format yang tidak dikenal.',
                 'raw_response' => substr($response, 0, 500),
                 'http_code' => $http_code
             ]);
        }

        return $this->response->setStatusCode($http_code)->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Endpoint untuk mengambil data Real Gadget Simad
     * 
     * URL: /api_npk/get_gadget
     */
    public function get_gadget()
    {
        $cookie_file = sys_get_temp_dir() . '/cookie_simad2.txt';

        $login_url = 'https://simad2.astra-agro.co.id/login';
        
        $ch_login = curl_init($login_url);
        curl_setopt($ch_login, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch_login, CURLOPT_POST, true);
        curl_setopt($ch_login, CURLOPT_POSTFIELDS, http_build_query([
            'username' => 'afirmansyah',
            'password' => 'andilee9'
        ]));
        curl_setopt($ch_login, CURLOPT_COOKIEJAR, $cookie_file);
        curl_setopt($ch_login, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch_login, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch_login, CURLOPT_FOLLOWLOCATION, true);
        
        curl_exec($ch_login);
        
        if (curl_errno($ch_login)) {
             $error_msg = curl_error($ch_login);
             curl_close($ch_login);
             return $this->response->setStatusCode(500)->setJSON([
                 'status' => 'error',
                 'message' => 'Gagal auto-login: ' . $error_msg
             ]);
        }
        curl_close($ch_login);

        $api_url = 'https://simad2.astra-agro.co.id/site/assign_pengguna/view/BIM';

        $headers = [
            'X-Requested-With: XMLHttpRequest',
            'Origin: https://simad2.astra-agro.co.id',
            'Referer: https://simad2.astra-agro.co.id/site/assign_pengguna'
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true); 
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);

        $response = curl_exec($ch);

        if(curl_errno($ch)){
            $error_msg = curl_error($ch);
            curl_close($ch);
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'cURL Error saat fetch data: ' . $error_msg
            ]);
        }

        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if (file_exists($cookie_file)) {
            @unlink($cookie_file);
        }

        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
             return $this->response->setStatusCode(500)->setJSON([
                 'status' => 'error',
                 'message' => 'Gagal parsing JSON dari API. Server membalas format yang tidak dikenal.',
                 'raw_response' => substr($response, 0, 500),
                 'http_code' => $http_code
             ]);
        }

        return $this->response->setStatusCode($http_code)->setJSON([
            'status' => 'success',
            'data' => $data
        ]);
    }
}
