<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous"> -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-Fo3rlrZj/k7ujTnHg4CGR2D7kSs0v4LLanw2qksYuRlEzO+tcaEPQogQ0KaoGN26/zrn20ImR1DfuLWnOo7aBA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <title>Document</title>
  <style>
    body {
      margin: 0;
      font-family: var(--bs-font-sans-serif);
      font-size: 1rem;
      font-weight: 400;
      line-height: 1.5;
      color: #212529;
      background-color: #fff;
      -webkit-text-size-adjust: 100%;
      -webkit-tap-highlight-color: transparent;
    }

    body {
      margin-top: 20px;
      color: #484b51;
      font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    }

    .bill .text-secondary-d1 {
      color: #728299;
    }

    .bill .page-header {
      margin: 0 0 1rem;
      padding-bottom: 1rem;
      padding-top: 0.5rem;
      border-bottom: 1px dotted #e2e2e2;
      display: -ms-flexbox;
      display: flex;
      -ms-flex-pack: justify;
      justify-content: space-between;
      -ms-flex-align: center;
      align-items: center;
    }

    .bill .page-title {
      padding: 0;
      margin: 0;
      font-size: 1.75rem;
      font-weight: 300;
    }

    .bill .brc-default-l1 {
      border-color: #dce9f0;
    }

    .bill .ml-n1,
    .bill .mx-n1 {
      margin-left: -0.25rem;
    }

    .bill .mr-n1,
    .bill .mx-n1 {
      margin-right: -0.25rem;
    }

    .bill .mb-4,
    .bill .my-4 {
      margin-bottom: 1.5rem;
    }

    .bill hr {
      margin-top: 1rem;
      margin-bottom: 1rem;
      border: 0;
      border-top: 1px solid rgba(0, 0, 0, 0.1);
    }

    .bill .text-grey-m2 {
      color: #888a8d;
    }

    .bill .text-success-m2 {
      color: #86bd68;
    }

    .bill .font-bolder,
    .bill .text-600 {
      font-weight: 600;
    }

    .bill .text-110 {
      font-size: 110%;
    }

    .bill .text-blue {
      color: #248b4b;
    }

    .bill .pb-25,
    .bill .py-25 {
      padding-bottom: 0.75rem;
    }

    .bill .pt-25,
    .bill .py-25 {
      padding-top: 0.75rem;
    }

    .bill .bgc-default-tp1 {
      background-color: #248b4b;
    }

    .bill .bgc-default-l4,
    .bill .bgc-h-default-l4:hover {
      background-color: #f3f8fa;
    }

    .bill .page-header .page-tools {
      -ms-flex-item-align: end;
      align-self: flex-end;
    }

    .bill .btn-light {
      color: #757984;
      background-color: #f5f6f9;
      border-color: #dddfe4;
    }

    .bill .w-2 {
      width: 1rem;
    }

    .bill .text-120 {
      font-size: 120%;
    }

    .bill .text-primary-m1 {
      color: #4087d4;
    }

    .bill .text-danger-m1 {
      color: #dd4949;
    }

    .bill .text-blue-m2 {
      color: #68a3d5;
    }

    .bill .text-150 {
      font-size: 150%;
      margin: 0;
    }

    .bill .text-60 {
      font-size: 60%;
    }

    .bill .text-grey-m1 {
      color: #7b7d81;
    }

    .bill .align-bottom {
      vertical-align: bottom;
    }

    .bill .bt_1 {
      border-bottom: 1px solid #248b4b;
      border-left: 1px solid #248b4b;
      border-right: 1px solid #248b4b;
    }

    .bill .bt-2 {
      border-bottom: 1px solid #248b4b;

    }

    .bill .bt-3 {
      border-left: 1px solid #248b4b;
      border-right: 1px solid #248b4b;
    }

    .bill hr {
      color: #248b4b;
    }

    .text-cts {
      color: #298c4f;

    }

    .bd-m {
      border: 1px solid #248b4b;
    }

    .header_mail {
      background: #248b4b;
    }

    .header_mail span {
      padding: 20px;
      display: block;
    }

    .header__mail .tt_mail {
      padding: 20px;
      display: block;
    }

    @media (min-width: 576px) {

      .container,
      .container-sm {
        max-width: 540px;
      }
    }

    @media (min-width: 768px) {

      .container,
      .container-md,
      .container-sm {
        max-width: 720px;
      }
    }

    @media (min-width: 992px) {

      .container,
      .container-lg,
      .container-md,
      .container-sm {
        max-width: 960px;
      }
    }

    @media (min-width: 1200px) {

      .container,
      .container-lg,
      .container-md,
      .container-sm,
      .container-xl {
        max-width: 1140px;
      }
    }

    @media (min-width: 1400px) {

      .container,
      .container-lg,
      .container-md,
      .container-sm,
      .container-xl,
      .container-xxl {
        max-width: 1320px;
      }
    }

    .container,
    .container-fluid,
    .container-lg,
    .container-md,
    .container-sm,
    .container-xl,
    .container-xxl {
      width: 100%;
      padding-right: var(--bs-gutter-x, .75rem);
      padding-left: var(--bs-gutter-x, .75rem);
      margin-right: auto;
      margin-left: auto;
    }

    @media (min-width: 992px) {
      .col-lg-6 {
        flex: 0 0 auto;
        width: 50%;
      }
    }

    .p-0 {
      padding: 0 !important;
    }

    .m-auto {
      margin: auto !important;
    }

    .text-white {
      color: #fff !important;
    }

    .d-block {
      display: block !important;
    }

    .row>* {
      flex-shrink: 0;
      width: 100%;
      max-width: 100%;
      padding-right: calc(var(--bs-gutter-x) * .5);
      padding-left: calc(var(--bs-gutter-x) * .5);
      margin-top: var(--bs-gutter-y);
    }

    .py-3 {
      padding-top: 1rem !important;
      padding-bottom: 1rem !important;
    }

    .row {
      --bs-gutter-x: 1.5rem;
      --bs-gutter-y: 0;
      display: flex;
      flex-wrap: wrap;
      margin-top: calc(var(--bs-gutter-y) * -1);
      margin-right: calc(var(--bs-gutter-x) * -.5);
      margin-left: calc(var(--bs-gutter-x) * -.5);
    }

    .col-lg-11 {
      flex: 0 0 auto;
      width: 91.66666667%;
    }

    @media (min-width: 768px) {
      .col-lg-11 {
        flex: 0 0 auto;
        width: 91.66666667%;
      }
    }

    @media (min-width: 992px) {
      .col-lg-11 {
        flex: 0 0 auto;
        width: 91.66666667%;
      }
    }

    .col-5 {
      flex: 0 0 auto;
      width: 41.66666667%;
    }

    @media (min-width: 768px) {
      .col-md-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
      }
    }

    @media (min-width: 992px) {
      .col-lg-5 {
        flex: 0 0 auto;
        width: 41.66666667%;
      }
    }

    .col-3 {
      flex: 0 0 auto;
      width: 25%;
    }

    @media (min-width: 768px) {
      .col-md-3 {
        flex: 0 0 auto;
        width: 25%;
      }
    }

    @media (min-width: 992px) {
      .col-lg-3 {
        flex: 0 0 auto;
        width: 25%;
      }
    }

    .col-4 {
      flex: 0 0 auto;
      width: 33.33333333%;
    }

    @media (min-width: 768px) {
      .col-md-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
      }
    }

    @media (min-width: 992px) {
      .col-lg-4 {
        flex: 0 0 auto;
        width: 33.33333333%;
      }
    }

    .d-block {
      display: block !important;
    }

    *,
    ::after,
    ::before {
      box-sizing: border-box;
    }

    .text-center {
      text-align: center !important;
    }


    .col-8 {
      flex: 0 0 auto;
      width: 66.66666667%;
    }

    @media (min-width: 768px) {
      .col-md-8 {
        flex: 0 0 auto;
        width: 66.66666667%;
      }
    }

    @media (min-width: 992px) {
      .col-lg-8 {
        flex: 0 0 auto;
        width: 66.66666667%;
      }
    }

    @media(max-width:767px) {
      .bill .text-150 {
        font-size: 18px;
      }

      .fz-mb {
        font-size: 14px !important;
      }

      .fz-mb-1 {
        font-size: 12px;
      }
    }

    @media(max-width:424px) {
      .bill .text-150 {
        font-size: 18px;
      }

      .fz-mb {
        font-size: 12px !important;
      }

      .fz-mb-1 {
        font-size: 12px;
      }
    }
  </style>
</head>

<body>
@dump($order)
  @php
  $date=date_format(date_create($order['created_at']) ,"d-m-Y H:i:s");
  $total=0;
  foreach($order['order_details'] as $p){
  $total+= $p['standard_price']*$p['quantity'];
  }
  $sale=0;
  if($order['voucher']){
    if($order['voucher']['classify_voucher_id']==3){
      $sale=$order['transportation_costs'];
    }else{
      $sale=$order['voucher']['sale']*$total/100;
    }
  
  }
  $pay=$total+$order['transportation_costs']-$sale;
  @endphp
  <section class="section-all bill">
    <div class="container">
      <div class="col-lg-6 m-auto bd-m p-0">
        <div class="header_mail">
          <span style="text-align: center;" class="text-150 text-white">Đơn hàng mới của khách hàng</span>
        </div>
        <div class="header__mail">
          <div class="tt_mail">
            <span class="d-block">Mã đơn hàng: <b>{{$order['code_orders']}}</b></span>
            <span class="d-block">Ngày tạo: <b>{{$date}}</b></span>
          </div>
        </div>
        <div class="col-lg-11 m-auto">
          <div class="mt-1">
            <div class="row text-600 text-white bgc-default-tp1 py-3">
              <div class="col-lg-5 col-md-5 col-5 text-center d-block fz-mb">Sản phẩm</div>
              <div class="col-lg-3 col-md-3 col-3 text-center d-block fz-mb">Số lượng</div>
              <div class="col-lg-4 col-md-4 col-4 text-center d-block fz-mb">Giá</div>
            </div>

            <div class="text-95 text-secondary-d3">
              @foreach($order['order_details'] as $p)
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-5 col-md-5 col-5 text-center d-block fz-mb-1">{{$p['standard_name']}}</div>
                <div class="col-lg-3 col-md-3 col-3 text-center d-block fz-mb-1">{{$p['quantity']}}</div>
                <div class="col-lg-4 col-md-4 col-4 text-center d-block fz-mb-1 text-secondary-d2 ">{{$p['standard_price']}} đ</div>
              </div>
              @endforeach
            </div>
            <div class="text-95 text-secondary-d3">
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-8 col-md-8 col-8 fz-mb">
                  Tổng cộng:
                </div>
                <div class="col-lg-4 col-md-4 col-4">
                  <span class="text-120 text-center d-block fz-mb">{{$total}} đ</span>
                </div>
              </div>
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-8 col-md-8 col-8 fz-mb">
                  Giá cước:
                </div>
                <div class="col-lg-4 col-md-4 col-4">
                  <span class="text-120 text-center d-block fz-mb">{{$order['transportation_costs']}} đ</span>
                </div>
              </div>
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-8 col-md-8 col-8 fz-mb">
                  Khuyến mãi: @if($order['voucher']) {{$order['voucher']['title']}} @else @endif
                </div>
                <div class="col-lg-4 col-md-4 col-4">
                  <span class="text-110 text-center d-block fz-mb">@if($sale) -{{$sale}} @else 0 @endif đ</span>
                </div>
              </div>
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-8 col-md-8 col-8 fz-mb">
                  Thanh toán:
                </div>
                <div class="col-lg-4 col-md-4 col-4">
                  <b><span class="text-110 text-center d-block fz-mb">{{$pay}} đ</span></b>
                </div>
              </div>
              <div class="row mb-sm-0 py-3 bt_1">
                <div class="col-lg-8 col-md-8 col-8 fz-mb">
                  Phương thức thanh toán:
                </div>
                <div class="col-lg-4 col-md-4 col-4">
                  <span class="text-110 text-center d-block fz-mb">@if($order['payments']==1) MOMO @else COD @endif</span>
                </div>
              </div>
            </div>
          </div>
          <div class="d-block mt-3">
            <p class="text-150 fz-mb">Thông tin khách hàng</p>
            <div class="row pt-3">
              <div class="col-lg-6 ">
                <p class="fz-mb">Tên khách hàng: <b>{{$order['customer_name']}} </b></p>
                <p class="fz-mb">Số điện thoại: <b>{{$order['customer_phone']}} </b></p>
                <p class="fz-mb">Địa chỉ: <b>{{$order['customer_address']}} </b></p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>
</body>

</html>