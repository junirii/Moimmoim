<?php

namespace application\controllers;

use application\libs\Application;

class UserController extends Controller
{
  public function join()
  {
    switch (getMethod()) {
      case _POST:
        $json = getJson();
        //아이디 중복 체크
        if ($this->model->selUser($json)) {
          return [_RESULT => 2];
        } else {
          $json["pw"] = password_hash($json["pw"], PASSWORD_BCRYPT);
          return [_RESULT => $this->model->insUser($json)];
        }
        return [_RESULT => 0];
    }
  }

  public function login()
  {
    switch (getMethod()) {
      case _POST:
        $json = getJson();
        $dbUser = $this->model->selUser($json);
        if (!$dbUser || !password_verify($json["pw"], $dbUser->pw)) {
          return [_RESULT => 0];
        } else {
          $dbUser->pw = null;
          $dbUser->regdt = null;
          $this->flash(_LOGINUSER, $dbUser);
          return [_RESULT => $dbUser];
        }
    }
  }

  public function logout()
  {
    switch (getMethod()) {
      case _POST:
        $this->flash(_LOGINUSER);
        return [_RESULT => 1];
    }
  }

  public function myPage()
  {
    $urlPaths = getUrlPaths();
    $param = [
      "iuser" => intval($urlPaths[2]) // iuser 타인으로 바꾸기
    ];

    $myPageHost = $this->model->myPageHost($param); // 함수 쓰는법
    $myPageTravelState = $this->model->myPageTravelState($param);
    $myPageCmt = $this->model->myPageCmt($param);
    $myPageTravelFav = $this->model->myPageTravelFav($param);
    $selUser = $this->model->selUser($param);

    $data = [
      "myPageTravelFav" => $myPageTravelFav,
      "myPageHost" => $myPageHost,
      "myPageTravelState" => $myPageTravelState,
      "myPageCmt" => $myPageCmt,
      "selUser" => $selUser,
    ];
    return [_RESULT => $data];
    // return $this->model->myPage($param);
  }

  public function myAccount()
  {
    $urlPaths = getUrlPaths();
    $param = [
      "iuser" => intval($urlPaths[2]) // iuser 타인으로 바꾸기
    ];

    $selUser = $this->model->selUser($param);

    $data = [
      "selUser" => $selUser,
    ];
    return [_RESULT => $data];
  }
}
