<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
include 'connection/mysqli_connection.php';

include('models/email.php');
include('models/phone.php');
include('models/branch.php');
include('models/customerGroup.php');
include('models/contact.php');
include('models/customer.php');

$sql = "SELECT * FROM contact";

$result = mysqli_query($con, $sql);
$rows = mysqli_num_rows($result);

$pageSize = 10;
$pages = ceil($rows / $pageSize);

// the current page number
if (!isset($_GET['page']) || !isset($_GET['pageSize'])) {
  $page = 1;
  $pageSize = 10;
}
else {
  $page = $_GET['page'];
  $pageSize = $_GET['pageSize'];
}

// LIMIT starting number for the records on the current page
$this_page_first_result = ($page - 1) * $pageSize;

$sql = "SELECT * FROM contact LIMIT " . $this_page_first_result . "," . $pageSize;

$result = mysqli_query($con, $sql);
$i = 0;
$response = array();

$status_code = "404";
$error_message = "Data not found";

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_array($result)) {

    if ($row['gender'] = "null") {
      $gender = 'Male';
    }
    else {
      $gender = $row['gender'];
    }

    if ($row['addressLine1'] = "null") {
      $addressLine1 = ucwords($row['city'] . ',' . $row['country']);
    }
    else {
      $addressLine1 = $row['addressLine1'];
    }

    if ($row['isActive'] == "1") {
      $isActive = true;
    }
    else {
      $isActive = false;
    }

    $contact = new Contact();
    $contact->id = $row['id'];
    $contact->name = ucwords($row['name']);
    $contact->gender = $gender;
    $contact->relationship = 'Parent';

    $email = new Email();
    $email->label = 'Work';
    $email->email = $row['email'];

    $phone = new Phone();
    $phone->label = 'Mobile';
    $phone->dialingCode = '+251';
    $phone->phone = $row['phone_1'];

    $contact->emails = array($email);
    $contact->phoneNumbers = array($phone);
    $contact->addressLine1 = $addressLine1;

    $response['page'] = $page;
    $response['pages'] = $pages;
    $response['pageSize'] = $pageSize;
    $response['rows'] = $rows;
    $response['data'][$i] = $contact;

    $i++;
  }
  echo json_encode($response, JSON_PRETTY_PRINT);
}
else {

  echo json_encode(array('status' => $status_code, 'message' => $error_message));
}

?>