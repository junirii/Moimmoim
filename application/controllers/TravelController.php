<?php
namespace application\controllers;
use application\libs\application;

class TravelController extends Controller{
    public function test() {
        // if()
    }
    // 리스트
    public function travelList() {
        $json = getJson();

        $param = [
            "arr_area" => $json["filter"]["selectedArea"],
            "s_date" => $json["filter"]["s_date"],
            "e_date" => $json["filter"]["e_date"],
            "f_people" => $json["filter"]["f_people"],
            "l_price" => $json["filter"]["l_price"],
            "h_price" => $json["filter"]["h_price"],
            "f_gender" => $json["filter"]["f_gender"],
            "f_age" => $json["filter"]["f_age"],
        ];
        return $this->model->travelList($param);
    }

    // FilterList
    public function travelFilterList() {
        $json = getJson();
        $param = [
            "area" => $json["area"],
            "location" => $json["location"],
            "s_date" => $json["s_date"],
            "e_date" => $json["e_date"],
            "people" => $json["people"],
            "gender" => $json["gender"],
            "age" => $json["age"],
            "l_price" => $json["l_price"],
            "h_price" => $json["h_price"]
        ];
        return $this->model->travelFilterList($param);
    }

    public function create() {
        $json = getJson();
        print_r($json);
        return [_RESULT => $this->model->travelInsert($json)];
    }

    // area list
    public function areaList() {
        return $this->model->areaList();
    }

    // location list
    public function locationList() {
        $urlPaths = getUrlPaths();
        if(count($urlPaths) !== 3) {
            exit();
        }        
        $param = [ "iarea" => $urlPaths[2] ];
        return $this->model->locationList($param);
    }
    
    // age List
    public function ageList() {
        return $this->model->ageList();
    }

    public function detail() {
        $urlPaths = getUrlPaths();
        $param = [
            "itravel" => intval($urlPaths[2])
        ];
        $hostUser = $this->model->selUserByItravel($param);
        $travelData = $this->model->selTravelByItravel($param);
        $day = $this->model->selDayByItravel($param);
        $ctnt = $this->model->selCtntByItravel($param);
        $data = [
            "hostUser" => $hostUser,
            "day" => $day,
            "ctnt" => $ctnt,
            "travelData" => $travelData
        ];
        return [_RESULT => $data];
    }
    // 좋아요한 게시물
    public function travelFav() {
        $urlPaths = getUrlPaths();
        $iuser = $urlPaths[2];
        $param = [
            "iuser" => $iuser,
        ];
        switch (getMethod()) {
            case _GET:
                return [_RESULT => $this->model->selTravelFav($param)];
            case _POST:
                $itravel = $urlPaths[3];
                $param["itravel"] = $itravel;
                return [_RESULT => $this->model->TravelUserFav($param)];
            case _DELETE:
                $itravel = $urlPaths[3];
                $param["itravel"] = $itravel;
                return [_RESULT => $this->model->travelDeleteFav($param)];           
        }
    }

    public function TravelUserFav() {
        $urlPaths = getUrlPaths();
        $param = [
            "iuser" => intval($urlPaths[2])
        ];
        return [_RESULT => $this->model->TravelUserFav($param)];
    }

    public function travelDeleteFav() {
        $urlPaths = getUrlPaths();
        $param = [
            "iuser" => intval($urlPaths[2])
        ];
        return [_RESULT => $this->model->travelDeleteFav($param)];
    }

    public function insTravelAndCtnt(){
        switch (getMethod()) {
            case _POST:
                $json = getJson();
                $image_parts = explode(";base64,", $json["travel"]["main_img"]);
                $image_type_aux = explode("image/", $image_parts[0]);
                $image_type = $image_type_aux[1];
                $image_base64 = base64_decode($image_parts[1]);
                $fileNm = uniqid() . "." . $image_type;

                $param = [
                    "iuser" => $json["travel"]["iuser"],
                    "title" => $json["travel"]["title"],
                    "area" => $json["travel"]["area"],
                    "location" => $json["travel"]["location"],
                    "main_img" => $fileNm,
                    "s_date" => $json["travel"]["s_date"],
                    "e_date" => $json["travel"]["e_date"],
                    "f_people" => $json["travel"]["f_people"],
                    "f_price" => $json["travel"]["f_price"],
                    "f_gender" => $json["travel"]["f_gender"],
                    "f_age" => $json["travel"]["f_age"],
                ];
                $itravel = $this->model->travelInsert($param);
                if($itravel){
                    $dirPath = _IMG_PATH . "/travel/" . $json["travel"]["iuser"] . "/main";
                    $filePath = $dirPath . "/" . $fileNm;
                    if(!is_dir($dirPath)) {
                        mkdir($dirPath, 0777, true);
                    }
                    $result = file_put_contents($filePath, $image_base64);
                }
        }
    }

    public function uploadMainImg() {
        // $itravel = 
        $json = getJson();
        $image_parts = explode(";base64,", $json["image"]);
        $image_type_aux = explode("image/", $image_parts[0]);
        $image_type = $image_type_aux[1];
        $image_base64 = base64_decode($image_parts[1]);
        $dirPath = _IMG_PATH . "/travel/" . $json["travel"]["iuser"] . "/main";
        $fileNm = uniqid() . "." . $image_type;
        $filePath = $dirPath . "/" . $fileNm;
        if(!is_dir($dirPath)) {
            mkdir($dirPath, 0777, true);
        }
        $result = file_put_contents($filePath, $image_base64);
        // if($result){
        //     $param = [
        //       "product_id" => $productId,
        //       "type" => $type,
        //       "path" => $fileNm
        //     ];
        //     $this->model->productImageInsert($param);
        // }
        return [_RESULT => $result ? 1 : 0];
    }
    
}