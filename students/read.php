<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: PUT, GET, POST, DELETE");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
header('Content-Type: application/json');
include '../connection/mysqli_connection.php';

include('../models/email.php');
include('../models/phone.php');
include('../models/branch.php');
include('../models/customerGroup.php');
include('../models/contact.php');
include('../models/customer.php');

$sql = "SELECT * FROM students";

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

$sql = "SELECT * FROM students LIMIT " . $this_page_first_result . "," . $pageSize;

$result = mysqli_query($con, $sql);
$i = 0;
$response = array();

$status_code = "404";
$error_message = "Data not found";

if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_array($result)) {

    // echo json_encode($row);

    if ($row['isActive'] == "1") {
      $isActive = true;
    }
    else {
      $isActive = false;
    }

    $branch = new Branch();
    $branch->id = 1;
    $branch->name = ucwords('main campus');


    $customerGroup = new CustomerGroup();
    $customerGroup->id = ucwords($row['grade']);
    $customerGroup->name = ucwords($row['grade']);
    $customerGroup->orderNo = $row['id'];

    $email = new Email();
    $email->label = ucwords('work');
    $email->email = $row['email'];

    $phone = new Phone();
    $phone->label = ucwords('mobile');
    $phone->dialingCode = '+251';
    $phone->phone = $row['fatherPhone'];

    $contact = new Contact();
    $contact->id = $row['id'];
    $contact->name = ucwords($row['fatherName']);
    $contact->gender = ucwords($row['sex']);
    $contact->relationship = 'Parent';
    $contact->emails = array($email);
    $contact->phoneNumbers = array($phone);
    $contact->primaryEmail = $row['email'];
    $contact->primaryPhone = $row['motherPhone'];

    $customer = new Customer();
    $customer->id = $row['id'];
    $customer->name = ucwords($row['firstName'] . ' ' . $row['fatherName'] . ' ' . $row['lastName']);
    $customer->isActive = $isActive;
    $customer->branch = $branch;
    $customer->customerGroup = $customerGroup;
    $customer->contacts = array($contact);

    $response['page'] = $page;
    $response['pages'] = $pages;
    $response['pageSize'] = $pageSize;
    $response['rows'] = $rows;
    $response['data'][$i] = $customer;

    $i++;
  }
  echo json_encode($response, JSON_PRETTY_PRINT);
}
else {

  echo json_encode(array('status' => $status_code, 'message' => $error_message));
}
?>