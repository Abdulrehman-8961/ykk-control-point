<!DOCTYPE html>
<html lang="{{$system_language}}">
  <head>
      @include("layouts.scripts")
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>FIRST HSE</title>
    <link rel="apple-touch-icon" sizes="180x180" href="{{asset('public')}}/media/favicon/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="{{asset('public')}}/media/favicon/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('public')}}/media/favicon/favicon-16x16.png">
    <link rel="manifest" href="{{asset('public')}}/media/favicon/site.webmanifest">
    <link rel="mask-icon" href="{{asset('public')}}/media/favicon/safari-pinned-tab.svg" color="#ffffff">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="theme-color" content="#ffffff">
    <link rel="stylesheet" href="{{asset('public')}}/assets/css/bootstrap.min.css" />
    <link
      rel="stylesheet"
      href="{{asset('public')}}/assets/bootstrap-datepicker/css/bootstrap-datepicker.css"
    />

     <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;700;800;900&display=swap" rel="stylesheet">
    <!--<link
      rel="stylesheet"
      href="{{asset('public')}}/assets/bootstrap-datepicker/css/bootstrap-datepicker.standalone.css"
    />-->
    <link rel="stylesheet" href="{{asset('public')}}/assets/css/style.css" />
    <link rel="stylesheet" href="{{asset('public')}}/assets/css/risk-assessment-list.css" />
    <link rel="stylesheet" href="{{asset('public')}}/assets/css/risk-assessment.css" />
    <link
      rel="stylesheet"
      href="{{asset('public')}}/assets/bootstrap-datepicker/css/customize.css"
    />
    <link rel="stylesheet" href="{{asset('public')}}/assets/css/style.scss">
    <style>
      .select-engineering-event{
        cursor: pointer;
      }
      .plus-engineering-control{
        background-color: #fff !important;
      }
      .ue-two {
    line-height: 33px !important;
}

.selected-list{
  background-color: #0B4068 !important;
  color: #fff !important;
}
.border-app-selected-list{
  border-color: #0B4068 !important
}

    </style>
  </head>
  <body class="app-bg">
    @php
        $hazard_language = [
            'Mechanical' => trans('app.hazard.mechanical'),
            'Electrical' => trans('app.ihazard.electrical'),
            'Thermal' => trans('app.hazard.thermal'),
            'Pressure' => trans('app.hazard.pressure'),
            'Vibrations' => trans('app.hazard.vibration'),
            'Power Magnet' => trans('app.hazard.power_mangnet'),
            'Chemical' => trans('app.hazard.chemical'),
            'Physical' => trans('app.hazard.physical'),
            'Biological' => trans('app.hazard.biological'),
            'Noise' => trans('app.hazard.noise'),
            'Magnetic Fields' => trans('app.hazard.magnetic_fields'),
            'Radiations' => trans('app.hazard.radiations'),
            'Falls' => trans('app.hazard.falls'),
            'Fall of Objects' => trans('app.hazard.falls_of_objects'),
            'Ergonomics' => trans('app.hazard.ergonomics'),
            'Psychological (Mental Health)'=> trans('app.hazard.psychological'),
            'Store Energy' => trans('app.hazard.stored_energy'),
            'Lone Worker' => trans('app.hazard.lone_worker'),
            'Environment' => trans('app.hazard.environment'),
            'Weather' => trans('app.hazard.weather')
  ];
  $signature_selects = [
    'Validation' => trans('app.manager.signatures.validation'),
    'Annual Review' =>  trans('app.manager.signatures.annual_review'),
    'Feedback from Events' => trans('app.manager.signatures.feedback'),
    'Management of change' => trans('app.manager.signatures.management'),
    'Safety Alert' => trans('app.manager.signatures.safety_alert'),
    'Lessons learned' => trans('app.manager.signatures.lessions'),
  ];
  $monitering_selects = [
    "EX Atmosphere monitering" => trans('app.manager.monitering.ex_atm'),
    "Noise Level monitering" => trans('app.manager.monitering.noise_level'),
    "Oxygen Level monitering" => trans('app.manager.monitering.oxygen'),
    "Airflow monitering" => trans('app.manager.monitering.airflow'),
    "OEL (Occupational Exposer Level)" => trans('app.manager.monitering.oel'),
    "Heat Monitering" => trans('app.manager.monitering.heat'),
    "Humidity Monitering" => trans('app.manager.monitering.humidity'),
    "Other" => trans('app.manager.monitering.other'),
  ];
  $signage_selects = [
    "Safety Sign" => trans('app.manager.signage.safety_sign'),
    "Color Code labeling" => trans('app.manager.signage.color_code_labeling'),
    "Floor Marking" => trans('app.manager.signage.floor_marking'),
  ];
  $testing_selects = [
    "select" => trans('app.manager.test.select'),
    "Energy test" => trans('app.manager.tests.energy_test'),
    "Isolation test" => trans('app.manager.tests.isolation_test'),
    "Power test" => trans('app.manager.tests.power_test'),
    "Pressure test" => trans('app.manager.tests.pressure_test'),
    "Capacity test" => trans('app.manager.tests.capacity_test'),
    "Load test" => trans('app.manager.tests.load_test'),
    "Integrity test" => trans('app.manager.tests.integrity_test'),
    "Corrosion test" => trans('app.manager.tests.corrosion_test'),
    "Other" => trans('app.manager.tests.other'),
  ];
    @endphp
    <main class="container-fluid app px-0">
            <div class="container-fluid  main-content ">
            <div class="row pt-2 ">
            <div class="col-lg-12 mb-3">
    <div class="container-fluid">
        <ul class="nav row">
            <li class="nav-item col-12 px-0">
              <div class="nav-link nav-link-tab-readonly text-decoration-none text-center container-fluid" style="font-weight: 200 !important" href="javascript:;">
                <div class="row">
                    <div class="col-2 operation-text">#RA000{{$operation->reference}}</div>
                <div class="col-10 operation-text">{{$operation->operation_name}}</div>
                </div>
              </div>
            </li>
          </ul>
    </div>
            </div>
          <div class="col-lg-12 col-md-12 ">
            <ul class="nav nav-tabs nav-justified mx-3" id="myTab" role="tablist">
              <li class="nav-item page-section mx-2" data-target="general-information">
                <a
                  class="nav-link nav-link-tab text-decoration-none pb-1"
                  data-bs-toggle="tab"
                  id="general-information-tab"
                  data-bs-target="#general-information"
                  type="button"
                  role="tab"
                  aria-controls="general-information"
                  aria-selected="false"
                  >@lang('app.manager.general_information')</a
                >
              </li>
              <li class="nav-item page-section mx-2" data-target="conformity">
                <a
                  class="nav-link nav-link-tab text-decoration-none pb-1"
                  data-bs-toggle="tab"
                  id="conformity-tab"
                  data-bs-target="#conformity"
                  type="button"
                  role="tab"
                  aria-controls="conformity"
                  aria-selected="false"
                  >@lang('app.manager.conformity')</a
                >
              </li>
              <li class="nav-item page-section mx-2" data-target="risk-assessment">
                <a
                  class="nav-link active nav-link-tab text-decoration-none pb-1"
                  data-bs-toggle="tab"
                  id="risk-assessment-tab"
                  data-bs-target="#risk-assessment"
                  type="button"
                  role="tab"
                  aria-controls="risk-assessment"
                  aria-selected="true"
                  >@lang('app.manager.risk_management')</a
                >
              </li>
              <li class="nav-item page-section mx-2" data-target="ppe">
                <a
                  class="nav-link nav-link-tab text-decoration-none pb-1"
                  data-bs-toggle="tab"
                  id="ppe-tab"
                  data-bs-target="#ppe"
                  type="button"
                  role="tab"
                  aria-controls="ppe"
                  aria-selected="false"
                  >@lang('app.manager.ppe')</a
                >
              </li>
            </ul>
            <div class="tab-content" id="myTabContent">
              <div
                class="tab-pane fade mt-2"
                id="general-information"
                role="tabpanel"
                aria-labelledby="general-information-tab"
                tabindex="0"
              >
                <div class="container-fluid">
                  <div class="row mt-3">
                    <div class="col-lg-3 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                            <h6 class="mb-0 py-1 page-col-text-header header-general-information">@lang('app.manager.position')</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col text-center p-1">
                            <div class="container bg-white ">
                              <div class="row">
                                <div class="col-md-12 px-4 bg-white">
                                  <input
                                    class="form-control form-control-sm task-control py-0 px-0 fs-13 header-general-information" value="{{@$operation->job_position}}"
                                    id="position"
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                            <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                              @lang('app.manager.first_edition')
                            </h6>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-2 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col text-center p-1">
                            <h6 class="mb-0 py-1 text-dark bg-white fs-13 header-general-information">{{date('d/m/Y', strtotime($operation->created_at))}}</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-3 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                            <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                              @lang('app.manager.work_location')
                            </h6>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col text-center p-1">
                            <div class="container bg-white ">
                              <div class="row">
                                <div class="col-md-12 px-4 bg-white">
                                  <input
                                    class="form-control form-control-sm task-control fs-13 header-general-information py-0 px-0" value="{{@$operation->work_location}}"
                                    id="work_location"
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-3 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                            <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                              @lang('app.manager.last_update')
                            </h6>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-2 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col text-center p-1">
                            <h6 class="mb-0 py-1 text-dark bg-white fs-13 header-general-information">@if(@$operation->updated_at){{date('d/m/Y', strtotime(@$operation->updated_at))}}@endif</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-12 ps-0">
                      <h6
                        class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                      >
                        @lang('app.manager.hazard_people')
                      </h6>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-6 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-11 ps-0 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.execution_staff')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-1 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                            class="col-lg-12 px-0 page-col text-center justify-content-center p-1"
                          >
                            <input
                              class="form-control form-control-sm task-control text-center fs-13 header-general-information"
                              id="execution_staff"
                              @if(@$operation->execution_staff)
                              value="{{@$operation->execution_staff}}"
                              @else
                              value="0"
                              @endif
                              style="border: 0 !important"
                            />
                          </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-6 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-11 ps-1 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-lg-12 px-0 page-col-gray text-center p-1">
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.exposed_people')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-1 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-lg-12 px-0 page-col text-center p-1">
                                  <input
                                    class="form-control form-control-sm task-control text-center header-general-information fs-13"
                                    id="exposed_people"
                                    @if(@$operation->exposed_people)
                                    value="{{$operation->exposed_people}}"
                                    @else
                                    value="0"
                                    @endif
                                    style="border: 0 !important"
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-12 ps-0">
                      <h6
                        class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                      >
                        @lang('app.manager.asssessment_validation')
                      </h6>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-5 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-3 ps-0 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.action')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-9 ps-0 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.responsible')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-7 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-5 ps-0 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.position')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-2 ps-0 pe-1">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.date')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-5 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header header-general-information">
                                    @lang('app.manager.reason')
                                  </h6>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  @if($operation->general_information == 0)
                  <div class="row mt-1">
                    <div class="col-lg-5 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-3 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0"
                                    value="@lang('app.manager.creation')"
                                    readonly
                                  />
                                  <input
                                  type="hidden"
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0" signature-action="1"
                                    value="Creation"
                                    readonly
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-9 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0 signature-responsible" signature-responsible="1"
                                    value=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-7 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-5 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0 signature-position" signature-position="1"
                                    value=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-2 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0" signature-date="1"
                                    value="{{date('d/m/Y', strtotime($operation->created_at))}}"
                                    readonly
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-5 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control signature-reason py-2 fs-13 text-center px-0"
                                    value="@lang('app.manager.first_edition')"
                                    readonly
                                  />
                                  <input
                                  type="hidden"
                                    class="form-control form-control-sm signature-control signature-reason py-2 fs-13 text-center px-0"
                                    value="First Edition" signature-reason="1"
                                    readonly
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="container-fluid px-0" id="signatures">

                  </div>
                  @php
                  $signature_ids = array('1');
                @endphp
                  @else
                  @php
                    $signature_ids = array();
                  @endphp
                  <div class="container-fluid px-0" id="signatures">
                    @foreach (DB::table('signatures')
                    ->where('is_deleted', 0)
                    ->where('operation_reference', $operation->reference)
                    ->orderBy('id', 'asc')
                    ->get() as $signature
                     )
                     @php
                      array_push($signature_ids, strval($signature->id));
                     @endphp
                    <div class="row mt-1">
                      <div class="col-lg-5 px-0">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-lg-3 px-0">
                              <div class="container-fluid">
                                <div class="row ">
                                  <div class="col-lg-12 text-center bg-white p-1">
                                    @php
                                    $sign_action_name = $signature->action;
                                        if($signature->action == 'Creation'){
                                            $sign_action_name = trans('app.manager.creation');
                                        }
                                        if($signature->action == 'Update'){
                                            $sign_action_name = trans('app.manager.update');
                                        }
                                        if($signature->action == 'Validation'){
                                            $sign_action_name = trans('app.manager.validation');
                                        }
                                    @endphp
                                    <input
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0"
                                      value="{{$sign_action_name}}"
                                      readonly
                                    />
                                    <input
                                    type="hidden"
                                    class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0" signature-action="{{$signature->id}}"
                                    value="{{$signature->action}}"

                                  />
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-9 px-0">
                              <div class="container-fluid">
                                <div class="row ">
                                  <div class="col-lg-12 text-center bg-white p-1">
                                    <input
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0 signature-responsible" signature-responsible="{{$signature->id}}"
                                      value="{{$signature->responsible}}"
                                    />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-7 px-0">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-lg-5 px-0">
                              <div class="container-fluid">
                                <div class="row">
                                  <div class="col-lg-12 text-center bg-white p-1">
                                    <input
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0 signature-position" signature-position="{{$signature->id}}"
                                      value="{{$signature->position}}"
                                    />
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-2 px-0">
                              <div class="container-fluid">
                                <div class="row ">
                                  <div class="col-lg-12 text-center bg-white p-1">
                                    <input
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0" signature-date="{{$signature->id}}"
                                      value="{{date('d/m/Y', strtotime($signature->date))}}"
                                      readonly
                                    />
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="col-lg-5 px-0">
                              <div class="container-fluid">
                                <div class="row ">
                                  <div class="col-lg-12 text-center bg-white p-1 hstack">
                                    @php
                                    $sign_reason_name = $signature->reason;
                                    if($signature->reason == 'First Edition'){
                                        $sign_reason_name = trans('app.manager.first_edition');
                                    }
                                    @endphp
                                    @foreach ($signature_selects as $key => $value)
                                        @if ($key == $signature->reason)
                                            @php
                                                $sign_reason_name = $value;
                                            @endphp
                                        @endif
                                    @endforeach
                                    <input
                                    type="hidden"
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0 w-100"
                                      value="{{$signature->reason}}" signature-reason="{{$signature->id}}"
                                    />
                                    <input
                                      class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0 w-100"
                                      value="{{$sign_reason_name}}"
                                      readonly
                                    />
                                    @if($signature->reason != 'First Edition')
                                    <a class="bg-white text-decoration-none pe-2 delete-signature pb-0" style="padding-bottom: 2px !important;" signature-key="{{$signature->id}}" href="javascript:;">
                                        <span>x</span>
                                    </a>
                                    @endif
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </div>
                  @endif
                  <div class="row mt-1">
                    <div class="col-lg-4 px-0 mt-1 p-1">
                      <button
                        class="btn btn-yellow fs-13" id="add-signature"

                      >
                        @lang('app.manager.new_update')
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="tab-pane fade mt-2"
                id="conformity"
                role="tabpanel"
                aria-labelledby="conformity-tab"
                tabindex="0"
              >
                <div class="container-fluid">
                  <div class="row mt-3">
                    <div class="col-lg-12 ps-0">
                      <h6
                        class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                      >
                        @lang('app.manager.pre_job_req') <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                        data-bs-content="
                        @lang('app.bubble.manager.pre_job_requirement')
                        "
                        >
                      </h6>
                    </div>
                  </div>
                  @if($operation->conformity == 0)
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">@lang('app.manager.people')</h6> <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info bg-white me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                           @lang('app.bubble.manager.people')
                            "
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0">
                            <div class="form-check form-check-inline justify-content-center d-flex  ps-0 me-0 pe-0 mb-0">
                              <input
                                class="btn-check"
                                type="checkbox"
                                value="Authorized Only" name="people"
                                id="authorized-only"
                              />
                              <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="authorized-only">
                                @lang('app.manager.checks.authorization')
                              </label>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col p-1">
                            <div class="container-fluid">
                              <div class="row bg-white">
                                <div class="col-lg-12 bg-white">
                                  <input
                                    class="form-control form-control-sm signature-control border-pre-job-control py-0 text-center px-0 people-details mt-1" reference="Authorized Only"
                                    value=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex  ps-0 me-0 mb-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Permit to work" name="people"
                          id="permit-to-work"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="permit-to-work">
                          @lang('app.manager.checks.permit_to_work')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control border-pre-job-control py-0 text-center px-0 people-details mt-1" reference="Permit to work"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Medical Clearance" name="people"
                          id="medical-clearance"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="medical-clearance">
                          @lang('app.manager.checks.medical_clearance')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 people-details" reference="Medical Clearance"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Training / Certification" name="people"
                          id="training"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="training">
                          @lang('app.manager.checks.training')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 people-details" reference="Training / Certification"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">
                              @lang('app.manager.workplace')
                            </h6>
                            <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                           @lang('app.bubble.manager.workplace')
                            "
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Lighting" name="workplace"
                          id="lighting"
                        />
                        <label class="btn btn-outline-primary fs-13 w-100 check-rounded text-start conformity-check-ps" for="lighting">
                          @lang('app.manager.checks.lighting')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 workplace-details" reference="Lighting"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox" name="workplace"
                          value="Housekeeping"
                          id="housekeeping"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="housekeeping">
                          @lang('app.manager.checks.housekeeping')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 workplace-details" reference="Housekeeping"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div id="sigange-rows">@php  $signage_ids = array('1'); @endphp
                  <div class="row mt-2" reference="1">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                            <div class="container bg-transparent conformity-select-container">
                              <select
                              class="form-select form-select-sm conformity-select pt-0 pb-0 fs-13 signage-select" reference="1"
                            >
                              <option value="" selected>@lang('app.manager.checks.signage')</option>
                              <option value="Safety Sign">@lang('app.manager.signage.safety_sign')</option>
                              <option value="Color Code labeling">@lang('app.manager.signage.color_code_labeling')</option>
                              <option value="Floor Marking">@lang('app.manager.signage.floor_marking')</option>
                            </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 d-none">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                >
                                  <div
                                    class="container-fluid d-flex justify-content-start px-2 bg-white"

                                  >
                                    <input
                                      class="form-control form-control-sm signature-control border-pre-job-control py-0 mt-1 text-center px-0 signage-details" reference="1"
                                      value=""
                                    />
                                  </div>
                                  <a
                                    class="bg-white text-decoration-none ps-3 delete-signage" reference="1"
                                    href="javascript:;"
                                  >
                                    <img
                                      src="{{asset('public')}}/media/icons/cancel.png"
                                      class="text-xmark"
                                    />
                                  </a>
                                </div>
                                <!--
                                <div
                                  class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                                >
                                <a href="javascript:;" class="text-decoratiton-none plus-signage" reference="1">
                                  <img
                                    src="{{asset('public')}}/media/icons/plus-solid.svg"
                                    class="btn-xmark"
                                  />
                                </a>
                                </div>-->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div id="monitering-rows">@php  $monitering_ids = array('1'); @endphp
                  <div class="row mt-2" reference="1">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                            <div class="container bg-transparent conformity-select-container">
                              <select
                              class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 monitering-select" reference="1"
                            >
                              <option value="" selected>
                                @lang('app.manager.checks.monitering')
                              </option>
                              <option value="EX Atmosphere monitering">@lang('app.manager.monitering.ex_atm')</option>
                              <option value="Noise Level monitering">@lang('app.manager.monitering.noise_level')</option>
                              <option value="Oxygen Level monitering">@lang('app.manager.monitering.oxygen')</option>
                              <option value="Airflow monitering">@lang('app.manager.monitering.airflow')</option>
                              <option value="OEL (Occupational Exposer Level)">@lang('app.manager.monitering.oel')</option>
                              <option value="Heat Monitering">@lang('app.manager.monitering.heat')</option>
                              <option value="Humidity Monitering">@lang('app.manager.monitering.humidity')</option>
                              <option value="Other">@lang('app.manager.monitering.other')</option>
                            </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 d-none">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                >
                                  <div
                                    class="container-fluid d-flex justify-content-start px-2 bg-white"
                                  >
                                    <input
                                      class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 monitering-details" reference="1"
                                      value=""
                                    />
                                  </div>
                                  <a
                                    class="bg-white text-decoration-none ps-3 delete-monitering" reference="1"
                                    href="javascript:;"
                                  >
                                    <img
                                      src="{{asset('public')}}/media/icons/cancel.png"
                                      class="text-xmark"
                                    />
                                  </a>
                                </div>
                                <!--
                                <div
                                  class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                                >
                                  <a class="text-decoration-none plus-monitering" reference="1">
                                      <img
                                    src="{{asset('public')}}/media/icons/plus-solid.svg"
                                    class="btn-xmark"
                                  />
                                  </a>
                                </div>-->
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                  <div class="row mt-3">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">
                              @lang('app.manager.chemicals')
                            </h6>
                            <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.chemicals')
                            "
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray p-1">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="MSDS / Labeling" name="chemicals"
                          id="msds-labeling"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="msds-labeling">
                          @lang('app.manager.chekcs.labeling')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 chemical-details" reference="MSDS / Labeling"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Packing Integrity" name="chemicals"
                          id="packing-integrity"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="packing-integrity">
                          @lang('app.manager.checks.packing_integrity')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 chemical-details" reference="Packing Integrity"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Storage Conditions" name="chemicals"
                          id="storage-conditions"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="storage-conditions">
                          @lang('app.manager.checks.storage_conditions')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 chemical-details" reference="Storage Conditions"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1 text-center">
                      <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                        <input
                          class="btn-check"
                          type="checkbox"
                          value="Secondary Containment" name="chemicals"
                          id="secondary-containment"
                        />
                        <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="secondary-containment">
                          @lang('app.manager.checks.transportation')
                        </label>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center page-col p-1">
                      <div class="container-fluid">
                        <div class="row bg-white">
                          <div class="col-lg-12 bg-white">
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 chemical-details" reference="Secondary Containment"
                              value=""
                            />
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">
                              @lang('app.manager.tools')
                            </h6>
                            <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.tools')
                            "
                            >
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                            <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                <div id="equipment-tools"> @php $equipment_ids=array('1'); $testing_ids=array('1'); @endphp
                  <div class="row mt-2" reference-package="1">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div
                            class="col-lg-12 px-0 d-flex justify-content-between page-col-gray p-1"
                          >
                            <input type="text" value="" placeholder="Add a Name" reference-package="1" class=" package-name mb-0 py-1 text-dark bg-white fw-bold ps-3 fs-13 form-control form-control-sm border-0">
                            <a
                              class="text-decoration-none pe-2 delete-equipment" reference-package="1"
                              href="javascript:;"
                            >
                              <img src="{{asset('public')}}/media/icons/cancel.png" class="text-xmark" />
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center p-1"></div>
                    <div class="col-lg-12 px-0 p-1">
                        <div class="container-fluid">
                            <div class="row mt-2">
                                <div class="col-lg-4 ps-0 pe-1 text-center">
                                  <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                    <input
                                      class="btn-check"
                                      type="checkbox"
                                      value="Certification" name="equipment" reference-package="1"
                                      id="certification"
                                    />
                                    <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="certification">
                                      @lang('app.manager.checks.certification')
                                    </label>
                                  </div>
                                </div>
                                <div class="col-lg-8 px-0 text-center page-col p-1">
                                  <div class="container-fluid">
                                    <div class="row bg-white">
                                      <div class="col-lg-12 bg-white">
                                        <input
                                          class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 equipment-details" reference="Certification" reference-package="1"
                                          value=""
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row mt-2">
                                <div class="col-lg-4 ps-0 pe-1 text-center">
                                  <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                    <input
                                      class="btn-check"
                                      type="checkbox" reference-package="1"
                                      value="Calibration" name="equipment"
                                      id="calibration"
                                    />
                                    <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="calibration">
                                      @lang('app.manager.checks.calibration')
                                    </label>
                                  </div>
                                </div>
                                <div class="col-lg-8 px-0 text-center page-col p-1">
                                  <div class="container-fluid">
                                    <div class="row bg-white">
                                      <div class="col-lg-12 bg-white">
                                        <input
                                          class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 equipment-details" reference="Calibration" reference-package="1"
                                          value=""
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="equipment-testing" reference-package="1">
                                <div class="row mt-2" reference="1" reference-package="1">
                                    <div class="col-lg-4 ps-0 pe-1">
                                      <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                                        <div class="container bg-transparent conformity-select-container">
                                          <input type="hidden" name="equipment" value="Testing" reference="1" reference-package="1">
                                          <select
                                            class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select" reference="1" reference-package="1"
                                          >
                                            <option value="" selected>@lang('app.manager.test.select')</option>
                                            <option value="Integrity test">@lang('app.manager.tests.integrity_test')</option>
                                            <option value="Energy test">@lang('app.manager.tests.energy_test')</option>
                                            <option value="Isolation test">@lang('app.manager.tests.isolation_test')</option>
                                            <option value="Power test">@lang('app.manager.tests.power_test')</option>
                                            <option value="Pressure test">@lang('app.manager.tests.pressure_test')</option>
                                            <option value="Capacity test">@lang('app.manager.tests.capacity_test')</option>
                                            <option value="Load test">@lang('app.manager.tests.load_test')</option>
                                            <option value="Corrosion test">@lang('app.manager.tests.corrosion_test')</option>
                                            <option value="Other">@lang('app.manager.tests.other')</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-lg-8 px-0 text-center d-none">
                                      <div class="container-fluid">
                                        <div class="row">
                                          <div
                                            class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                          >
                                            <div
                                              class="container-fluid px-2 d-flex justify-content-start bg-white"
                                            >
                                              <input
                                                class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details" reference="1" reference-package="1"
                                                value=""
                                              />
                                            </div>

                                            <a
                                              class="bg-white text-decoration-none ps-3 delete-testing-list" reference="1" reference-package="1"
                                              href="javascript:;"
                                            >
                                              <img
                                                src="{{asset('public')}}/media/icons/cancel.png"
                                                class="text-xmark"
                                              />
                                            </a>
                                          </div>
                                          <!--<div
                                            class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                                          >
                                          <a class="text-decoration-none plus-testing-list" reference="1" reference-package="1">
                                            <img
                                              src="{{asset('public')}}/media/icons/plus-solid.svg"
                                              class="btn-xmark"
                                            />
                                        </a>
                                          </div>-->
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                              </div>
                        </div>
                    </div>
                  </div>

                </div>
                @else
                <div class="row mt-2">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">@lang('app.manager.people')</h6>
                          <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.people')
                            "
                            >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @foreach (
                DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('type', 'people')
                ->get()
                as $check
                )
                    <div class="row mt-2">
                      <div class="col-lg-4 ps-0 pe-1">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-lg-12 px-0">
                              <div class="form-check form-check-inline justify-content-center d-flex  ps-0 me-0 pe-0 mb-0">
                                <input
                                  class="btn-check"
                                  type="checkbox"
                                  value="{{$check->field}}" name="people"
                                  id="people{{$loop->iteration}}"
                                  @if($check->checked == 1)
                                  checked
                                  @endif
                                />
                                <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="people{{$loop->iteration}}">
                                    @if($check->field == 'Authorized Only')
                                    @lang('app.manager.checks.authorization')
                                    @endif
                                    @if($check->field == 'Permit to work')
                                    @lang('app.manager.checks.permit_to_work')
                                    @endif
                                    @if($check->field == 'Medical Clearance')
                                    @lang('app.manager.checks.medical_clearance')
                                    @endif
                                    @if($check->field == 'Training / Certification')
                                    @lang('app.manager.checks.training')
                                    @endif
                                </label>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="col-lg-8 px-0">
                        <div class="container-fluid">
                          <div class="row">
                            <div class="col-lg-12 px-0 text-center page-col p-1">
                              <div class="container-fluid">
                                <div class="row bg-white">
                                  <div class="col-lg-12 bg-white">
                                    <input
                                      class="form-control form-control-sm signature-control border-pre-job-control py-0 text-center px-0 people-details mt-1" reference="{{$check->field}}"
                                      value="{{$check->detail}}"
                                    />
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                @endforeach

                <div class="row mt-2">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">@lang('app.manager.workplace')</h6>
                          <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.workplace')
                            "
                            >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @foreach (
                DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('type', 'workplace')
                ->get()
                as $check
                )
                    <div class="row mt-2">
                      <div class="col-lg-4 ps-0 pe-1 text-center">
                        <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                          <input
                            class="btn-check"
                            type="checkbox"
                            value="{{$check->field}}" name="workplace"
                            id="workplace{{$loop->iteration}}"
                            @if($check->checked == 1)
                            checked
                            @endif
                          />
                          <label class="btn btn-outline-primary fs-13 w-100 check-rounded text-start conformity-check-ps" for="workplace{{$loop->iteration}}">
                            @if($check->field == 'Lighting')
                            @lang('app.manager.checks.lighting')
                            @endif
                            @if($check->field == 'Housekeeping')
                            @lang('app.manager.checks.housekeeping')
                            @endif
                          </label>
                        </div>
                      </div>
                      <div class="col-lg-8 px-0 text-center page-col p-1">
                        <div class="container-fluid">
                          <div class="row bg-white">
                            <div class="col-lg-12 bg-white">
                              <input
                                class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 workplace-details" reference="{{$check->field}}"
                                value="{{$check->detail}}"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                @endforeach
                @if(count(DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('type', 'Signage')
                ->get()) > 0)
                <div id="sigange-rows">@php  $signage_ids = array(); @endphp
                @foreach (DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('type', 'Signage')
                ->get() as $item)
                @php
                  array_push($signage_ids, strval($item->id));
                @endphp
                <div class="row mt-2" reference="{{$item->id}}">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 page-col p-1 check-rounded @if($item->field != '') selected-list @endif">
                          <div class="container bg-transparent conformity-select-container">
                            <select
                            class="form-select form-select-sm conformity-select pt-0 pb-0 fs-13 signage-select @if($item->field != '') selected-list border-app-selected-list @endif" reference="{{$item->id}}"
                            >
                              <option value="" selected>@lang('app.manager.checks.signage')</option>
                              <option value="Safety Sign" @if($item->field == 'Safety Sign') selected @endif>@lang('app.manager.signage.safety_sign')</option>
                              <option value="Color Code labeling" @if($item->field == 'Color Code labeling') selected @endif>@lang('app.manager.signage.color_code_labeling')</option>
                              <option value="Floor Marking" @if($item->field == 'Floor Marking') selected @endif>@lang('app.manager.signage.floor_marking')</option>
                            </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 @if($item->field == '' || $item->field == null) d-none @endif">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center">
                          <div class="container-fluid">
                            <div class="row">
                              <div
                                class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                              >
                                <div
                                  class="container-fluid d-flex justify-content-start px-2 bg-white"

                                >
                                  <input
                                    class="form-control form-control-sm signature-control border-pre-job-control py-0 mt-1 text-center px-0 signage-details" reference="{{$item->id}}"
                                    value="{{$item->detail}}"
                                  />
                                </div>
                                <a
                                  class="bg-white text-decoration-none ps-3 delete-signage" reference="1"
                                  href="javascript:;"
                                >
                                  <img
                                    src="{{asset('public')}}/media/icons/cancel.png"
                                    class="text-xmark"
                                  />
                                </a>
                              </div>
                              <!--
                              <div
                                class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                              >
                              <a href="javascript:;" class="text-decoratiton-none plus-signage" reference="1">
                                <img
                                  src="{{asset('public')}}/media/icons/plus-solid.svg"
                                  class="btn-xmark"
                                />
                              </a>
                              </div>-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                @endforeach
                </div>
              @else
              <div id="sigange-rows">@php  $signage_ids = array('1'); @endphp
                <div class="row mt-2" reference="1">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                          <div class="container bg-transparent conformity-select-container">
                            <select
                            class="form-select form-select-sm conformity-select pt-0 pb-0 fs-13 signage-select" reference="1"
                          >
                            <option value="" selected>@lang('app.manager.checks.signage')</option>
                            <option value="Safety Sign">@lang('app.manager.signage.safety_sign')</option>
                            <option value="Color Code labeling">@lang('app.manager.signage.color_code_labeling')</option>
                            <option value="Floor Marking">@lang('app.manager.signage.floor_marking')</option>
                          </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 d-none">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center">
                          <div class="container-fluid">
                            <div class="row">
                              <div
                                class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                              >
                                <div
                                  class="container-fluid d-flex justify-content-start px-2 bg-white"

                                >
                                  <input
                                    class="form-control form-control-sm signature-control border-pre-job-control py-0 mt-1 text-center px-0 signage-details" reference="1"
                                    value=""
                                  />
                                </div>
                                <a
                                  class="bg-white text-decoration-none ps-3 delete-signage" reference="1"
                                  href="javascript:;"
                                >
                                  <img
                                    src="{{asset('public')}}/media/icons/cancel.png"
                                    class="text-xmark"
                                  />
                                </a>
                              </div>
                              <!--
                              <div
                                class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                              >
                              <a href="javascript:;" class="text-decoratiton-none plus-signage" reference="1">
                                <img
                                  src="{{asset('public')}}/media/icons/plus-solid.svg"
                                  class="btn-xmark"
                                />
                              </a>
                              </div>-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              @if(count(DB::table('pre_job_requirement_checks')
              ->where('is_deleted', 0)
              ->where('operation_reference', $operation->reference)
              ->where('type', 'Monitering')
              ->get()) > 0)
              <div id="monitering-rows">@php  $monitering_ids = array(); @endphp
              @foreach (DB::table('pre_job_requirement_checks')
              ->where('is_deleted', 0)
              ->where('operation_reference', $operation->reference)
              ->where('type', 'Monitering')
              ->get() as $item)
              @php
                array_push($monitering_ids, strval($item->id));
              @endphp
                <div class="row mt-2" reference="{{$item->id}}">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 page-col p-1 check-rounded @if($item->field != '') selected-list @endif">
                          <div class="container bg-transparent conformity-select-container">
                            <select
                            class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 monitering-select @if($item->field != '') selected-list border-app-selected-list @endif" reference="{{$item->id}}"
                          >
                            <option value="" selected>
                              @lang('app.manager.checks.monitering')
                            </option>
                            <option value="EX Atmosphere monitering" @if($item->field == 'EX Atmosphere monitering') selected @endif>@lang('app.manager.monitering.ex_atm')</option>
                            <option value="Noise Level monitering" @if($item->field == 'Noise Level monitering') selected @endif>@lang('app.manager.monitering.noise_level')</option>
                            <option value="Oxygen Level monitering" @if($item->field == 'Oxygen Level monitering') selected @endif>@lang('app.manager.monitering.oxygen')</option>
                            <option value="Airflow monitering" @if($item->field == 'Airflow monitering') selected @endif>@lang('app.manager.monitering.airflow')</option>
                            <option value="OEL (Occupational Exposer Level)" @if($item->field == 'OEL (Occupational Exposer Level)') selected @endif>@lang('app.manager.monitering.oel')</option>
                            <option value="Heat Monitering" @if($item->field == 'Heat Monitering') selected @endif>@lang('app.manager.monitering.heat')</option>
                            <option value="Humidity Monitering" @if($item->field == 'Humidity Monitering') selected @endif>@lang('app.manager.monitering.humidity')</option>
                            <option value="Other" @if($item->field == 'Other') selected @endif>@lang('app.manager.monitering.other')</option>
                          </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 @if ($item->field == '' || $item->field == null)
                    d-none
                @endif">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center">
                          <div class="container-fluid">
                            <div class="row">
                              <div
                                class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                              >
                                <div
                                  class="container-fluid d-flex justify-content-start px-2 bg-white"
                                >
                                  <input
                                    class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 monitering-details" reference="{{$item->id}}"
                                    value="{{$item->detail}}"
                                  />
                                </div>
                                <a
                                  class="bg-white text-decoration-none ps-3 delete-monitering" reference="1"
                                  href="javascript:;"
                                >
                                  <img
                                    src="{{asset('public')}}/media/icons/cancel.png"
                                    class="text-xmark"
                                  />
                                </a>
                              </div>
                              <!--
                              <div
                                class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                              >
                                <a class="text-decoration-none plus-monitering" reference="1">
                                    <img
                                  src="{{asset('public')}}/media/icons/plus-solid.svg"
                                  class="btn-xmark"
                                />
                                </a>
                              </div>-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

              @endforeach
            </div>
              @else
              <div id="monitering-rows">@php  $monitering_ids = array('1'); @endphp
                <div class="row mt-2" reference="1">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                          <div class="container bg-transparent conformity-select-container">
                            <select
                            class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 monitering-select" reference="1"
                          >
                            <option value="" selected>
                              @lang('app.manager.checks.monitering')
                            </option>
                            <option value="EX Atmosphere monitering">@lang('app.manager.monitering.ex_atm')</option>
                            <option value="Noise Level monitering">@lang('app.manager.monitering.noise_level')</option>
                            <option value="Oxygen Level monitering">@lang('app.manager.monitering.oxygen')</option>
                            <option value="Airflow monitering">@lang('app.manager.monitering.airflow')</option>
                            <option value="OEL (Occupational Exposer Level)">@lang('app.manager.monitering.oel')</option>
                            <option value="Heat Monitering">@lang('app.manager.monitering.heat')</option>
                            <option value="Humidity Monitering">@lang('app.manager.monitering.humidity')</option>
                            <option value="Other">@lang('app.manager.monitering.other')</option>
                          </select>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 d-none">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center">
                          <div class="container-fluid">
                            <div class="row">
                              <div
                                class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                              >
                                <div
                                  class="container-fluid d-flex justify-content-start px-2 bg-white"
                                >
                                  <input
                                    class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 monitering-details" reference="1"
                                    value=""
                                  />
                                </div>
                                <a
                                  class="bg-white text-decoration-none ps-3 delete-monitering" reference="1"
                                  href="javascript:;"
                                >
                                  <img
                                    src="{{asset('public')}}/media/icons/cancel.png"
                                    class="text-xmark"
                                  />
                                </a>
                              </div>
                              <!--
                              <div
                                class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                              >
                                <a class="text-decoration-none plus-monitering" reference="1">
                                    <img
                                  src="{{asset('public')}}/media/icons/plus-solid.svg"
                                  class="btn-xmark"
                                />
                                </a>
                              </div>-->
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endif
              <div class="row mt-2">
                <div class="col-lg-4 ps-0 pe-1">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                        <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">@lang('app.manager.chemicals')</h6>
                        <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.chemicals')
                            "
                            >
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-8 px-0">
                  <div class="container-fluid">
                    <div class="row">
                      <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                        <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
                @foreach (
                DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('type', 'chemicals')
                ->get()
                as $check
                )
                    <div class="row mt-2">
                      <div class="col-lg-4 ps-0 pe-1 text-center">
                        <div class="form-check form-check-inline justify-content-center d-flex mb-0  ps-0 me-0 pe-0">
                          <input
                            class="btn-check"
                            type="checkbox"
                            value="{{$check->field}}" name="chemicals"
                            id="chemicals{{$loop->iteration}}"
                            @if($check->checked == 1)
                            checked
                            @endif
                          />
                          <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="chemicals{{$loop->iteration}}">
                            @if ($check->field == 'MSDS / Labeling')
                            @lang('app.manager.chekcs.labeling')
                            @endif
                            @if ($check->field == 'Packing Integrity')
                            @lang('app.manager.checks.packing_integrity')
                            @endif
                            @if ($check->field == 'Storage Conditions')
                            @lang('app.manager.checks.storage_conditions')
                            @endif
                            @if ($check->field == 'Secondary Containment')
                            @lang('app.manager.checks.transportation')
                            @endif
                          </label>
                        </div>
                      </div>
                      <div class="col-lg-8 px-0 text-center page-col p-1">
                        <div class="container-fluid">
                          <div class="row bg-white">
                            <div class="col-lg-12 bg-white">
                              <input
                                class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 chemical-details" reference="{{$check->field}}"
                                value="{{$check->detail}}"
                              />
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                @endforeach
                <div class="row mt-2">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1 hstack">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold w-100">@lang('app.manager.tools')</h6>
                          <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="info me-2" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.tools')
                            "
                            >
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0">
                    <div class="container-fluid">
                      <div class="row">
                        <div class="col-lg-12 px-0 text-center page-col-gray pre-job-checks-header p-1">
                          <h6 class="mb-0 py-1 page-col-text-header fw-bold">@lang('app.manager.details')</h6>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              @if(DB::table('equipments')->where('is_deleted', 0)->where('operation_reference', $operation->reference)->count() > 0)
              <div id="equipment-tools"> @php $equipment_ids=array(); $testing_ids=array(); @endphp
              @foreach (DB::table('equipments')->where('is_deleted', 0)->where('operation_reference', $operation->reference)->get() as $package)
@php
  array_push($equipment_ids, strval($package->id));
@endphp
  <div class="row mt-2" reference-package="{{$package->id}}">
    <div class="col-lg-4 ps-0 pe-1">
      <div class="container-fluid">
        <div class="row">
          <div
            class="col-lg-12 px-0 d-flex justify-content-between page-col-gray p-1"
          >
          <input type="text" value="{{$package->equipment_name}}" placeholder="Add a Name" reference-package="{{$package->id}}" class=" package-name mb-0 py-1 text-dark bg-white fw-bold ps-3 fs-13 form-control form-control-sm border-0">
            <a
              class="text-decoration-none pe-2 delete-equipment" reference-package="{{$package->id}}"
              href="javascript:;"
            >
              <img src="{{asset('public')}}/media/icons/cancel.png" class="text-xmark" />
            </a>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8 px-0 text-center p-1"></div>
    <div class="col-lg-12 px-0 p-1">
        <div class="container-fluid">
              @foreach (DB::table('pre_job_requirement_checks')
              ->where('is_deleted', 0)
              ->where('operation_reference', $operation->reference)
              ->where('equipment', $package->id)
              ->where('type', 'certification')
              ->get()
              as $check )

              <div class="row mt-2">
                <div class="col-lg-4 ps-0 pe-1 text-center">
                  <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                    <input
                      class="btn-check"
                      type="checkbox"
                      value="Certification" name="equipment" reference-package="{{$package->id}}"
                      id="certification{{$package->id}}"
                      @if($check->checked == 1)
                      @checked(true)
                      @endif
                    />
                    <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="certification{{$package->id}}">
                        @lang('app.manager.checks.certification')
                    </label>
                  </div>
                </div>
                <div class="col-lg-8 px-0 text-center page-col p-1">
                  <div class="container-fluid">
                    <div class="row bg-white">
                      <div class="col-lg-12 bg-white">
                        <input
                          class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 equipment-details" reference="Certification" reference-package="{{$package->id}}"
                          value="{{$check->detail}}"
                        />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              @endforeach
              @foreach (DB::table('pre_job_requirement_checks')
              ->where('is_deleted', 0)
              ->where('operation_reference', $operation->reference)
              ->where('equipment', $package->id)
              ->where('type', 'calibration')
              ->get()
              as $check )
<div class="row mt-2">
  <div class="col-lg-4 ps-0 pe-1 text-center">
    <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
      <input
        class="btn-check"
        type="checkbox" reference-package="{{$package->id}}"
        value="Calibration" name="equipment"
        id="calibration{{$package->id}}"
        @if($check->checked == 1)
      @checked(true)
        @endif
      />
      <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="calibration{{$package->id}}">
        @lang('app.manager.checks.calibration')
      </label>
    </div>
  </div>
  <div class="col-lg-8 px-0 text-center page-col p-1">
    <div class="container-fluid">
      <div class="row bg-white">
        <div class="col-lg-12 bg-white">
          <input
            class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 equipment-details" reference="Calibration" reference-package="{{$package->id}}"
            value="{{$check->detail}}"
          />
        </div>
      </div>
    </div>
  </div>
</div>
              @endforeach

              <div class="equipment-testing" reference-package="{{$package->id}}">
                @if(DB::table('pre_job_requirement_checks')
                ->where('is_deleted', 0)
                ->where('operation_reference', $operation->reference)
                ->where('equipment', $package->id)
                ->where('type', 'testing')
                ->count() > 0)
@foreach (DB::table('pre_job_requirement_checks')
->where('is_deleted', 0)
->where('operation_reference', $operation->reference)
->where('equipment', $package->id)
->where('type', 'testing')
->get() as $item)
@php
array_push($testing_ids, strval($item->id));
@endphp
<div class="row mt-2" reference="{{$item->id}}" reference-package="{{$package->id}}">
  <div class="col-lg-4 ps-0 pe-1">
    <div class="col-lg-12 px-0 page-col p-1 check-rounded @if($item->field != '') selected-list @endif">
      <div class="container bg-transparent conformity-select-container">
        <input type="hidden" name="equipment" value="Testing" reference="{{$item->id}}" reference-package="{{$package->id}}">
        <select
          class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select @if($item->field != '') selected-list border-app-selected-list @endif" reference="{{$item->id}}" reference-package="{{$package->id}}"
        >
          <option value="" selected>@lang('app.manager.test.select')</option>
          <option value="Integrity test" @if($item->field == 'Integrity test') @selected(true) @endif>@lang('app.manager.tests.integrity_test')</option>
          <option value="Energy test" @if($item->field == 'Energy test') @selected(true) @endif>@lang('app.manager.tests.energy_test')</option>
          <option value="Isolation test" @if($item->field == 'Isolation test') @selected(true) @endif>@lang('app.manager.tests.isolation_test')</option>
          <option value="Power test" @if($item->field == 'Power test') @selected(true) @endif>@lang('app.manager.tests.power_test')</option>
          <option value="Pressure test" @if($item->field == 'Pressure test') @selected(true) @endif>@lang('app.manager.tests.pressure_test')</option>
          <option value="Capacity test" @if($item->field == 'Capacity test') @selected(true) @endif>@lang('app.manager.tests.capacity_test')</option>
          <option value="Load test" @if($item->field == 'Load test') @selected(true) @endif>@lang('app.manager.tests.load_test')</option>
          <option value="Corrosion test" @if($item->field == 'Corrosion test') @selected(true) @endif>@lang('app.manager.tests.corrosion_test')</option>
          <option value="Other" @if($item->field == 'Other') @selected(true) @endif>@lang('app.manager.tests.other')</option>
        </select>
      </div>
    </div>
  </div>
  <div class="col-lg-8 px-0 text-center @if ($item->field == '' || $item->field == null)
    d-none
@endif">
    <div class="container-fluid">
      <div class="row">
        <div
          class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
        >
          <div
            class="container-fluid px-2 d-flex justify-content-start bg-white"
          >
            <input
              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details " reference="{{$item->id}}" reference-package="{{$package->id}}"
              value="{{$item->detail}}"
            />
          </div>
          <a
            class="bg-white text-decoration-none ps-3 delete-testing-list" reference="1" reference-package="1"
            href="javascript:;"
          >
            <img
              src="{{asset('public')}}/media/icons/cancel.png"
              class="text-xmark"
            />
          </a>
        </div>
        <!--<div
          class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
        >
        <a class="text-decoration-none plus-testing-list" reference="1" reference-package="1">
          <img
            src="{{asset('public')}}/media/icons/plus-solid.svg"
            class="btn-xmark"
          />
      </a>
        </div>-->
      </div>
    </div>
  </div>
</div>
@endforeach
                @else
                <div class="row mt-2" reference="1" reference-package="{{$package->id}}">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                      <div class="container bg-transparent conformity-select-container">
                        <select
                          class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select" reference="1" reference-package="{{$package->id}}"
                        >
                          <option value="" selected>@lang('app.manager.test.select')</option>
                          <option value="Integrity test">@lang('app.manager.tests.integrity_test')</option>
                          <option value="Energy test">@lang('app.manager.tests.energy_test')</option>
                          <option value="Isolation test">@lang('app.manager.tests.isolation_test')</option>
                          <option value="Power test">@lang('app.manager.tests.power_test')</option>
                          <option value="Pressure test">@lang('app.manager.tests.pressure_test')</option>
                          <option value="Capacity test">@lang('app.manager.tests.capacity_test')</option>
                          <option value="Load test">@lang('app.manager.tests.load_test')</option>
                          <option value="Corrosion test">@lang('app.manager.tests.corrosion_test')</option>
                          <option value="Other">@lang('app.manager.tests.other')</option>
                        </select>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 text-center d-none">
                    <div class="container-fluid">
                      <div class="row">
                        <div
                          class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                        >
                          <div
                            class="container-fluid px-2 d-flex justify-content-start bg-white"
                          >
                            <input
                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details" reference="1" reference-package="{{$package->id}}"
                              value=""
                            />
                          </div>
                          <a
                            class="bg-white text-decoration-none ps-3 delete-testing-list" reference="1" reference-package="1"
                            href="javascript:;"
                          >
                            <img
                              src="{{asset('public')}}/media/icons/cancel.png"
                              class="text-xmark"
                            />
                          </a>
                        </div>
                        <!--<div
                          class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                        >
                        <a class="text-decoration-none plus-testing-list" reference="1" reference-package="1">
                          <img
                            src="{{asset('public')}}/media/icons/plus-solid.svg"
                            class="btn-xmark"
                          />
                      </a>
                        </div>-->
                      </div>
                    </div>
                  </div>
                </div>
                @endif
              </div>
        </div>
    </div>
  </div>


@endforeach
</div>
              @else
              <div id="equipment-tools"> @php $equipment_ids=array('1'); $testing_ids=array('1'); @endphp
                <div class="row mt-2" reference-package="1">
                  <div class="col-lg-4 ps-0 pe-1">
                    <div class="container-fluid">
                      <div class="row">
                        <div
                          class="col-lg-12 px-0 d-flex justify-content-between page-col-gray p-1"
                        >
                        <input type="text" value="" placeholder="Add a Name" reference-package="1" class=" package-name mb-0 py-1 text-dark bg-white fw-bold ps-3 fs-13 form-control form-control-sm border-0">
                          <a
                            class="text-decoration-none pe-2 delete-equipment" reference-package="1"
                            href="javascript:;"
                          >
                            <img src="{{asset('public')}}/media/icons/cancel.png" class="text-xmark" />
                          </a>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="col-lg-8 px-0 text-center p-1"></div>
                  <div class="col-lg-12 px-0 p-1">
                      <div class="container-fluid">
                          <div class="row mt-2">
                              <div class="col-lg-4 ps-0 pe-1 text-center">
                                <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                  <input
                                    class="btn-check"
                                    type="checkbox"
                                    value="Certification" name="equipment" reference-package="1"
                                    id="certification"
                                  />
                                  <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="certification">
                                    @lang('app.manager.checks.certification')
                                  </label>
                                </div>
                              </div>
                              <div class="col-lg-8 px-0 text-center page-col p-1">
                                <div class="container-fluid">
                                  <div class="row bg-white">
                                    <div class="col-lg-12 bg-white">
                                      <input
                                        class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 equipment-details" reference="Certification" reference-package="1"
                                        value=""
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="row mt-2">
                              <div class="col-lg-4 ps-0 pe-1 text-center">
                                <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                  <input
                                    class="btn-check"
                                    type="checkbox" reference-package="1"
                                    value="Calibration" name="equipment"
                                    id="calibration"
                                  />
                                  <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="calibration">
                                    @lang('app.manager.checks.calibration')
                                  </label>
                                </div>
                              </div>
                              <div class="col-lg-8 px-0 text-center page-col p-1">
                                <div class="container-fluid">
                                  <div class="row bg-white">
                                    <div class="col-lg-12 bg-white">
                                      <input
                                        class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 equipment-details" reference="Calibration" reference-package="1"
                                        value=""
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                            <div class="equipment-testing" reference-package="1">
                              <div class="row mt-2" reference="1" reference-package="1">
                                  <div class="col-lg-4 ps-0 pe-1">
                                    <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                                      <div class="container bg-transparent conformity-select-container">
                                        <input type="hidden" name="equipment" value="Testing" reference="1" reference-package="1">
                                        <select
                                          class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select" reference="1" reference-package="1"
                                        >
                                        <option value="" selected>@lang('app.manager.test.select')</option>
                                        <option value="Integrity test">@lang('app.manager.tests.integrity_test')</option>
                                        <option value="Energy test">@lang('app.manager.tests.energy_test')</option>
                                        <option value="Isolation test">@lang('app.manager.tests.isolation_test')</option>
                                        <option value="Power test">@lang('app.manager.tests.power_test')</option>
                                        <option value="Pressure test">@lang('app.manager.tests.pressure_test')</option>
                                        <option value="Capacity test">@lang('app.manager.tests.capacity_test')</option>
                                        <option value="Load test">@lang('app.manager.tests.load_test')</option>
                                        <option value="Corrosion test">@lang('app.manager.tests.corrosion_test')</option>
                                        <option value="Other">@lang('app.manager.tests.other')</option>
                                        </select>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-lg-8 px-0 text-center d-none">
                                    <div class="container-fluid">
                                      <div class="row">
                                        <div
                                          class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                        >
                                          <div
                                            class="container-fluid px-2 d-flex justify-content-start bg-white"
                                          >
                                            <input
                                              class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details" reference="1" reference-package="1"
                                              value=""
                                            />
                                          </div>
                                          <a
                                            class="bg-white text-decoration-none ps-3 delete-testing-list" reference="1" reference-package="1"
                                            href="javascript:;"
                                          >
                                            <img
                                              src="{{asset('public')}}/media/icons/cancel.png"
                                              class="text-xmark"
                                            />
                                          </a>
                                        </div>
                                        <!--<div
                                          class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                                        >
                                        <a class="text-decoration-none plus-testing-list" reference="1" reference-package="1">
                                          <img
                                            src="{{asset('public')}}/media/icons/plus-solid.svg"
                                            class="btn-xmark"
                                          />
                                      </a>
                                        </div>-->
                                      </div>
                                    </div>
                                  </div>
                                </div>
                            </div>
                      </div>
                  </div>
                </div>
              </div>
              @endif
                  @endif

                  <div class="row mt-2">
                    <div class="col-lg-4 px-0 mt-1 p-1">
                      <button
                        class="btn btn-yellow fs-13 plus-equipment"

                      >
                        @lang('app.manager.add_new')
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              <!-- START RISK ASSESSMENT -->
              <div
                class="tab-pane fade show active mt-2"
                id="risk-assessment"
                role="tabpanel"
                aria-labelledby="risk-assessment-tab"
                tabindex="0"
              >
                <div class="container-fluid mx-0 mt-4">
                  <ul class="nav row">
                    <li
                      class="nav-item col-2 ps-0 pe-1"
                    >
                      <div
                        class="nav-link nav-link-tab-readonly-dark justify-content-between border-dark d-flex gap-3 py-0 px-1"
                         index="-1"
                      >
                      <a class="text-decoration-none task-prev" index="-1" href="javascript:;">
                        <img
                          src="{{asset('public')}}/media/icons/arrow-right.svg" style="transform: rotate(180deg);"
                          class="arrow "
                        /></a>
                        <span class="text-dark task-sr" sr="0" style="font-size: 13px !important;line-height: 35px;font-weight:200;">@lang('app.manager.task') 1</span>
                        <a class="text-decoration-none task-next" index="1" href="javascript:;">
                        <img
                          src="{{asset('public')}}/media/icons/arrow-right.svg"
                          class="arrow "
                        /></a>
                  </div>
                    </li>
                    <li
                      class="nav-item col-10 px-0"
                    >
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-7 px-0">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-lg-12 px-0 ">
                                            <a
                                            class="nav-link nav-link-tab-readonly-white text-decoration-none app-gray-color text-center task-name" style="font-weight: 200 !important;color: #212529 !important;"
                                            href="javascript:;"
                                          >
                                            {{$tasks[0]->task}}
                                          </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </li>
                  </ul>
                  <div class="row mt-3">
                    <div class="col-lg-8 ps-0 pe-3 d-flex justify-content-between">
                        <h6 class="mb-0 engineering-controls ps-2 py-2 fs-13">
                            <b> @lang('app.manager.hazard_identification')</b> <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                            @lang('app.bubble.manager.hazard_identification')
                            "
                            >
                          </h6>
                        <a href="{{url('/hazard-identification/edit/'.$operation->reference)}}"><button
                           class="btn app-btn-primary-outline pt-2 pb-2 px-5 medium fs-13"
                           >
                        @lang('app.manager.edit')
                        </button></a>
                     </div>
                     <div class="col-lg-4 px-0">
                        <h6 class="mb-0 engineering-controls ps-2 py-2 fs-13">
                          <b> @lang('app.manager.engineering_controls')</b> <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                          data-bs-content="
                          @lang('app.bubble.manager.engineering_controls')
                          "
                          >
                        </h6>
                     </div>
                     <div class="col-lg-12 px-0">
                        <div class="container-fluid mt-2">
                           <div class="row header">
                                <div class="col-lg-2 ps-0 pe-1">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div
                                                class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                                <h6 class="mb-0 py-1 page-col-text-header">
                                                    @lang('app.manager.hazard')
                                                </h6>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-10 px-0">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div class="col-lg-7 px-0">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div
                                                            class="col-lg-12 px-0 page-col-gray text-center p-1"
                                                        >
                                                            <h6 class="mb-0 py-1 page-col-text-header">
                                                                @lang('app.manager.unwanted_events')
                                                            </h6>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 pe-0 ps-3">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div class="col-lg-4 px-0">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 px-0 page-col-gray text-center p-1"
                                                                    >
                                                                        <h6
                                                                            class="mb-0 py-1 page-col-text-header"
                                                                        >
                                                                            @lang('app.manager.controls')
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 pe-0 ps-1">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 px-0 page-col-gray text-center p-1"
                                                                    >
                                                                        <h6 class="mb-0 page-col-text-header py-1">
                                                                            @lang('app.manager.details')
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                           <!-- -->
                           <div class="row">
                                <div class="col-lg-2 ps-0 pe-1">
                                    <div class="container-fluid">
                                        <div class="row">
                                            <div
                                                class="col-lg-12 px-0 page-col-gray text-center p-1 mt-2 border-dark"
                                            >
                                                <div class="d-flex justify-content-between" style="padding-top:14px;padding-bottom:14px">
                                                 <div>
                                                    <a class="hazard-prev " href="javascript:;"><img
                                                        src="{{asset('public')}}/media/icons/arrow-right.svg" style="transform: rotate(180deg)"
                                                    class="arrow"
                                                    /></a>
                                                 </div>
                                                <div>
                                                    <span class=" text-dark hazard-text hazard-name fs-13" sr="0"
                                                >
                                                @php
                                                    $hazard_name = @$hazards[0]->task_hazard;
                                                @endphp
                                                @foreach ($hazard_language as $key => $value)
                                                    @if($key == @$hazards[0]->task_hazard)
                                                    @php
                                                    $hazard_name = $value;
                                                    @endphp
                                                    @endif
                                                @endforeach
                                                {{$hazard_name}}</span>
                                                </div>
                                                <div>
                                                    <a class="hazard-next " href="javascript:;"><img
                                                        src="{{asset('public')}}/media/icons/arrow-right.svg"
                                                        class="arrow"
                                                    /></a>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-10 px-0">
                                    @php
                                        $control_ids = array();
                                        $controls = array();
                                    @endphp
                                    <div class="container-fluid " id="unwanted_events">
                                        @foreach ($unwanted_events as $event)
                                        <div class="row" hazard-type="{{$event->hazard}}" event-row="{{$event->id}}">
                                            <div class="col-lg-7 px-0">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div
                                                            class="col-lg-12 px-0 page-col text-center p-1 mt-2"
                                                        >
                                                            <div class="hstack">
                                                                <h6 class="mb-0 py-1 text-dark bg-white px-1 w-100 event-defination">
                                                                    {{$event->event}}
                                                                </h6>
                                                                <div class="unwanted-event-details-text px-2 border-start cursor-pointer">
                                                                    @lang('app.manager.details')
                                                                    <img src="{{asset('public/media/icons/pointer.svg')}}" class="pointer">
                                                                </div>
                                                            </div>
                                                            <div class="px-5 details-input-box" style="display: none;">
                                                                <input
                                                                    class="form-control form-control-sm task-control py-0 px-0 unwanted-event-details" value="{{@$event->hazard_identification_details}}" event-id="{{$event->id}}"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-lg-5 pe-0 ps-3">
                                                <div class="container-fluid" engineering-control="{{$event->id}}">
                                                  @if($operation->risk_assessment == 1)
                                                  @if(DB::table('hazard_identification_engineering_controls as ec')
                                                  ->where('ec.is_deleted', 0)
                                                  ->where('ec.unwanted_event_ref', $event->id)
                                                  ->where('ec.operation_reference', $operation->reference)
                                                  ->select(
                                                      "ec.*",
                                                      "c.control as control",
                                                  )
                                                  ->Join("engineering_controls as c", "ec.engineering_control", "=", "c.id")
                                                  ->count() == 0)
                                                  <div class="row" control-id="{{$loop->iteration}}" @php array_push($control_ids, $loop->iteration); @endphp>
                                                    <div class="col-lg-4 px-0">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="{{$loop->iteration}}" data="{{$event->id}}" unwanted_event_id="{{$event->unwanted_event}}"
                                                                >
                                                                    <h6 class="mb-0 py-1 text-dark bg-white py-1">
                                                                        @lang('app.manager.select')
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <div class="row" r_id="{{$loop->iteration}}" e_id="{{$event->id}}" ue_id="{{$event->unwanted_event}}">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8 pe-0 ps-1">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                                                                >
                                                                    <div class="container bg-white">
                                                                        <div class="row">
                                                                            <div class="col-md-12 bg-white pe-0">
                                                                                <div class="container-fluid">
                                                                                    <div class="row">
                                                                                        <div class="col-10 bg-white">
                                                                                            <input
                                                                                                class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details" control-row-id="{{$loop->iteration}}" data="{{$event->id}}"
                                                                                                id="event-detail"
                                                                                            />
                                                                                        </div>
                                                                                        <div class="col-2 bg-white pe-0">
                                                                                            <a
                                                                                                class="bg-white text-decoration-none remove-strategy-of-controls" data="{{$event->id}}" ref-id="{{$loop->iteration}}"
                                                                                                href="javascript:;"
                                                                                            >
                                                                                                <img
                                                                                                    src="{{asset('public')}}/media/icons/cancel.png"
                                                                                                    class="text-xmark"
                                                                                                />
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-lg-1 pe-0 text-center  mt-2 "
                                                                >
                                                                    <div class="btn btn-sm app-btn-selection-outline">
                                                                        <img
                                                                        src="{{asset('public')}}/media/icons/plus-solid.svg"
                                                                        class="btn-xmark plus-engineering-control bg-white" unwanted_event_id="{{$event->unwanted_event}}" data="{{$event->id}}"
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                  @else
                                                  @foreach (
                                                  DB::table('hazard_identification_engineering_controls as ec')
                                                  ->where('ec.is_deleted', 0)
                                                  ->where('ec.unwanted_event_ref', $event->id)
                                                  ->where('ec.operation_reference', $operation->reference)
                                                  ->select(
                                                      "ec.*",
                                                      "c.control as control",
                                                  )
                                                  ->Join("engineering_controls as c", "ec.engineering_control", "=", "c.id")
                                                  ->get() as $item
                                                  )
                                                  <div class="row" control-id="{{$loop->iteration.''.$event->id}}" @php array_push($control_ids, $loop->iteration.''.$event->id); array_push($controls, $item->id); @endphp>
                                                    <div class="col-lg-4 px-0">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="{{$loop->iteration.''.$event->id}}" cname="{{$item->control}}" cvalue="{{$item->engineering_control}}" cdetails="{{$item->details}}"  data="{{$event->id}}" unwanted_event_id="{{$event->unwanted_event}}"
                                                                >
                                                                    <h6 class="mb-0 py-1 text-dark bg-white py-1">
                                                                        {{$item->control}}
                                                                    </h6>
                                                                </div>
                                                            </div>
                                                            <div class="row" r_id="{{$loop->iteration.''.$event->id}}" e_id="{{$event->id}}" ue_id="{{$event->unwanted_event}}">

                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-8 pe-0 ps-1">
                                                        <div class="container-fluid">
                                                            <div class="row">
                                                                <div
                                                                    class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                                                                >
                                                                    <div class="container bg-white">
                                                                        <div class="row">
                                                                            <div class="col-md-12 bg-white pe-0">
                                                                                <div class="container-fluid">
                                                                                    <div class="row">
                                                                                        <div class="col-10 bg-white">
                                                                                            <input
                                                                                                class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details" value="{{$item->details}}" control-row-id="{{$loop->iteration.''.$event->id}}" data="{{$event->id}}"
                                                                                                id="event-detail"
                                                                                            />
                                                                                        </div>
                                                                                        <div class="col-2 bg-white pe-0">
                                                                                            <a
                                                                                                class="bg-white text-decoration-none remove-strategy-of-controls" data="{{$event->id}}" ref-id="{{$loop->iteration.''.$event->id}}"
                                                                                                href="javascript:;"
                                                                                            >
                                                                                                <img
                                                                                                    src="{{asset('public')}}/media/icons/cancel.png"
                                                                                                    class="text-xmark"
                                                                                                />
                                                                                            </a>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                                <div
                                                                    class="col-lg-1 pe-0 text-center mt-2"
                                                                >
                                                                    <div class="btn btn-sm app-btn-selection-outline">
                                                                        <img
                                                                            src="{{asset('public')}}/media/icons/plus-solid.svg"
                                                                            class="btn-xmark plus-engineering-control bg-white" unwanted_event_id="{{$event->unwanted_event}}" data="{{$event->id}}"
                                                                        />
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                  @endforeach
                                                  @endif
                                                  @else
                                                    <div class="row" control-id="{{$loop->iteration}}" @php array_push($control_ids, $loop->iteration); @endphp>
                                                        <div class="col-lg-4 px-0">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="{{$loop->iteration}}" data="{{$event->id}}" unwanted_event_id="{{$event->unwanted_event}}"
                                                                    >
                                                                        <h6 class="mb-0 py-1 text-dark bg-white py-1">
                                                                            @lang('app.manager.select')
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                                <div class="row" r_id="{{$loop->iteration}}" e_id="{{$event->id}}" ue_id="{{$event->unwanted_event}}">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 pe-0 ps-1">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                                                                    >
                                                                        <div class="container bg-white ">
                                                                            <div class="row">
                                                                                <div class="col-md-12 bg-white pe-0">
                                                                                    <div class="container-fluid">
                                                                                        <div class="row">
                                                                                            <div class="col-10 bg-white">
                                                                                                <input
                                                                                                    class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details" control-row-id="{{$loop->iteration}}" data="{{$event->id}}"
                                                                                                    id="event-detail"
                                                                                                />
                                                                                            </div>
                                                                                            <div class="col-2 bg-white pe-0">
                                                                                                <a
                                                                                                    class="bg-white text-decoration-none remove-strategy-of-controls" data="{{$event->id}}" ref-id="{{$loop->iteration}}"
                                                                                                    href="javascript:;"
                                                                                                >
                                                                                                    <img
                                                                                                        src="{{asset('public')}}/media/icons/cancel.png"
                                                                                                        class="text-xmark"
                                                                                                    />
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="col-lg-1 pe-0 text-center mt-2"
                                                                    >
                                                                        <div class="btn btn-sm app-btn-selection-outline">
                                                                            <img
                                                                                src="{{asset('public')}}/media/icons/plus-solid.svg"
                                                                                class="btn-xmark plus-engineering-control bg-white" unwanted_event_id="{{$event->unwanted_event}}" data="{{$event->id}}"
                                                                            />
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                           <!-- -->
                        </div>
                     </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-12 ps-0">
                      <h6
                        class="mb-0 administrative-controls fw-bolder ps-4 py-2 fs-13"
                      >
                        @lang('app.manager.instructions_controls') <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                        data-bs-content="
                        @lang('app.bubble.manager.administrator_controls')
                        "
                        >
                      </h6>
                    </div>
                    <div class="col-lg-12 ps-0">
                      <div class="container-fluid mt-3">
                        <div class="row">
                          <div class="col-lg-6 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header">
                                    @lang('app.manager.obligation')
                                  </h6>
                                </div>
                              </div>
                              <div class="row mt-2" id="obligationsContainer">

                              </div>
                            </div>
                          </div>
                          <div class="col-lg-6 pe-0">
                            <div class="container-fluid pe-0">
                              <div class="row">
                                <div
                                  class="col-lg-12 px-0 page-col-gray text-center p-1"
                                >
                                  <h6 class="mb-0 py-1 page-col-text-header">
                                    @lang('app.manger.organization')
                                  </h6>
                                </div>
                              </div>
                              <div class="row mt-2" id="organizationsContainer">

                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div class="row mt-2">
                    <div class="col-lg-12 px-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div
                                      class="col-lg-3 px-0 page-col-gray text-center p-1"
                                    >
                                      <h6 class="mb-0 py-1 page-col-text-header">
                                        @lang('app.manager.controls')
                                      </h6>
                                    </div>
                                    <div
                                      class="col-lg-9 px-0 page-col-gray text-center p-1"
                                    >
                                      <h6 class="mb-0 py-1 page-col-text-header">
                                        @lang('app.manager.instructions')
                                      </h6>
                                    </div>
                            </div>
                        </div>
                    </div>
                  </div>
                  <div class="row" id="administrative-controls-details">

                  </div>
                </div>
              </div>
              <!-- END RISK ASSESSMENT -->
              <div
                class="tab-pane fade mt-2"
                id="ppe"
                role="tabpanel"
                aria-labelledby="ppe-tab"
                tabindex="0"
              >
                <div class="container-fluid">
                  <div class="row mt-3">
                    <div class="col-lg-12 ps-0">
                      <h6
                        class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                      >
                        @lang('app.manager.ppe_list') <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                        data-bs-content="
                        @lang('app.bubble.manager.operation_ppe')
                        "
                        >
                      </h6>
                    </div>
                  </div>
                  <div class="row mt-3">
                    <div class="col-lg-12 p-1 bg-white rounded">
                        <div class="row">
                            <div class="col-1 text-end" style="width: 4.33333333% !important;">
                            </div>
                            <div class="col-11 ps-0" style="width: 95.666667% !important;">
                                <div class="container-fluid">
                                    <div class="row">
                                      <div class="col ps-0 pe-1">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.protection')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-3 ps-0 pe-1">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.type')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-3 ps-0 pe-1">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.specifications')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col ps-0 pe-1">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.brand')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col ps-0 pe-1">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.reference')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div
                                              class="col-lg-12 px-0 page-col-gray text-center p-1"
                                            >
                                              <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                                @lang('app.manager.quantity')
                                              </h6>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12 p-1 bg-white rounded" id="operation-ppe">
                      @if(DB::table('ppe')->where('operation_reference', $operation->reference)->where('is_deleted', 0)->count() == 0)
                      <div class="row">
                        <div class="col-1 text-end" style="width: 4.33333333% !important;">
                            <span class="ppe-remove" operation-ppe-key="1">x</span>
                        </div>
                        <div class="col-11 ps-0" style="width: 95.666667% !important;">
                            <div class="container-fluid" operation="1">
                                <div class="row">
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <select
                                            class="form-select form-select-sm ppe-select text-center py-0 protection" protection="1"
                                          >
                                            <option value="" selected="">List</option>
                                            @foreach ($protections as $protection)
                                                <option value="{{$protection->id}}">{{$protection->name}}</option>
                                            @endforeach
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-3 px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <select
                                            class="form-select form-select-sm ppe-select text-center py-0 type" type="1"
                                          >
                                            <option value="" selected="">List</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col-3 px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0 specifications" specifications="1"
                                            placeholder=""
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0 brand" brand="1"
                                            placeholder=""
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0 reference" ref="1"
                                            placeholder=""
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                          type="number"
                                            class="form-control form-control-sm signature-control py-2 text-center px-0 quantity" quantity="1"
                                            placeholder=""
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                        </div>
                      </div>
                      @else
                      @php $company_ids = array(); @endphp
                        @foreach (
                        DB::table('ppe')->where('operation_reference', $operation->reference)->where('is_deleted', 0)->get()
                        as $item)
                        @php
                        array_push($company_ids, $item->id);
                        @endphp
                        <div class="row">
                            <div class="col-1 text-end" style="width: 4.33333333% !important;">
                                <span class="ppe-remove" operation-ppe-key="{{$item->id}}">x</span>
                            </div>
                            <div class="col-11 ps-0" style="width: 95.666667% !important;">
                                <div class="container-fluid" operation="{{$item->id}}">
                                    <div class="row">
                                      <div class="col px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <select
                                                class="form-select form-select-sm ppe-select text-center py-0 protection" protection="{{$item->id}}"
                                              >
                                                <option value="" selected="">List</option>
                                                @foreach ($protections as $protection)
                                                    <option value="{{$protection->id}}" @if($protection->id == $item->protection) selected @endif>{{$protection->name}}</option>
                                                @endforeach
                                              </select>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-3 px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <select
                                                class="form-select form-select-sm ppe-select text-center py-0 type" type="{{$item->id}}"
                                              >
                                                <option value="" selected="">List</option>
                                                @php
                                                     $as_lan = '';
                  switch ($system_language) {
                      case 'de':
                        $as_lan = 'german_';
                        break;
                      case 'fr':
                          $as_lan = 'french_';
                        break;
                      case 'it':
                          $as_lan = 'italian_';
                        break;
                      case 'pt':
                          $as_lan = 'portuguese_';
                          break;
                      case 'spa':
                          $as_lan = 'spanish_';
                          break;

                      default:
                          $as_lan = '';
                    }
                                                @endphp
                                                @foreach (DB::table('types')->where('is_deleted', 0)->select("id","protection",$as_lan."name as name")->where('protection', $item->protection)->get() as $type)
                                                    <option value="{{$type->id}}" @if($type->id == $item->type) selected @endif>{{$type->name}}</option>
                                                @endforeach
                                              </select>
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col-3 px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <input
                                                class="form-control form-control-sm signature-control py-2 text-center px-0 specifications" specifications="{{$item->id}}"
                                                placeholder="" value="{{$item->specifications}}"
                                              />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <input
                                                class="form-control form-control-sm signature-control py-2 text-center px-0 brand" brand="{{$item->id}}"
                                                placeholder="" value="{{$item->brand}}"
                                              />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <input
                                                class="form-control form-control-sm signature-control py-2 text-center px-0 reference" ref="{{$item->id}}"
                                                placeholder="" value="{{$item->reference}}"
                                              />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                      <div class="col px-0">
                                        <div class="container-fluid bg-white">
                                          <div class="row">
                                            <div class="col-lg-12 text-center bg-white p-1">
                                              <input
                                              type="number"
                                                class="form-control form-control-sm signature-control py-2 text-center px-0 quantity" quantity="{{$item->id}}"
                                                placeholder="" value="{{$item->quantity}}"
                                              />
                                            </div>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
                        </div>
                        @endforeach
                      @endif
                    </div>
                  </div>
                  <div class="row mt-5">
                    <div class="col-lg-4 px-0 p-1">
                        <button
                          class="btn btn-yellow fs-13 px-4   plus-operation-ppe"

                        >
                          @lang('app.manager.add_new')
                        </button>
                      </div>
                  </div>
                  <div class="row mt-3 d-none">
                    <div class="col-lg-5 ps-0">
                      <div class="container-fluid">
                        <div class="row mb-3">
                          <div class="col-lg-12 ps-0">
                            <h6
                              class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                            >
                              Company's PPE <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info" style="width: 14px" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                              data-bs-content="
                              <p class='bg-white'>
                                Automatic summary of all <span class='fw-bold app-primary-color bg-white'>PPE</span> used in the company, with addition of <span class='fw-bold app-primary-color bg-white'>quantities</span> , can be useful when creating <span class='fw-bold app-primary-color bg-white'>SoW</span> for future PPE acquisition or purchase.
Add a <span class='fw-bold app-primary-color bg-white'>date of next purchase</span> to every item in order to have visibility on next purchased and be alerted when time comes

                              </p>
                              "
                              >
                            </h6>
                          </div>
                        </div>
                      </div>
                      <div class="container-fluid">
                        <div class="row p-1 rounded bg-white" >
                          <div class="col-lg-12 container-fluid bg-white" style="    min-height: 154px !important;" id="company-ppe">
                            <div class="row">
                                  <div class="col ps-0 pe-1">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div
                                          class="col-lg-12 px-0 page-col-gray text-center p-1"
                                        >
                                          <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                            @lang('app.manager.type')
                                          </h6>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col ps-0 pe-1">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div
                                          class="col-lg-12 px-0 page-col-gray text-center p-1"
                                        >
                                          <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                            @lang('app.manager.brand')
                                          </h6>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col ps-0 pe-1">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div
                                          class="col-lg-12 px-0 page-col-gray text-center p-1"
                                        >
                                          <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                            @lang('app.manager.reference')
                                          </h6>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row ">
                                        <div
                                          class="col-lg-12 px-0 page-col-gray text-center p-1"
                                        >
                                          <h6 class="mb-0 py-1 page-col-text-header header-ppe">
                                            @lang('app.manager.quantity')
                                          </h6>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
                            @if(DB::table('ppe')->where('operation_reference', $operation->reference)->where('is_deleted', 0)->count() > 0)

                            @foreach (DB::table('ppe as p')
                            ->where('p.operation_reference', $operation->reference)
                            ->where('p.is_deleted', 0)
                            ->where('t.is_deleted', 0)
                            ->groupBy('p.reference')
                            ->select(
                              'p.*',
                              't.name as type_name',
                              DB::raw('SUM(p.quantity) as grouped_qty'),
                            )
                            ->leftJoin('types as t', 'p.type', '=', 't.id')
                            ->get() as $item)

                            <div class="row " company="{{$item->id}}">
                              <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div class="col-lg-12 text-center bg-white p-1">
                                        <input
                                          class="form-control form-control-sm signature-control py-2 text-center px-0" value="{{$item->type_name}}" company-type="" readonly
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div class="col-lg-12 text-center bg-white p-1">
                                        <input
                                          class="form-control form-control-sm signature-control py-2 text-center px-0" value="{{$item->brand}}" company-brand="" readonly
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div class="col-lg-12 text-center bg-white p-1">
                                        <input
                                          class="form-control form-control-sm signature-control py-2 text-center px-0" value="{{$item->reference}}" company-reference="" readonly
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                                <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div class="col-lg-12 text-center bg-white p-1">
                                        <input
                                          class="form-control form-control-sm signature-control py-2 text-center px-0" value="{{$item->grouped_qty}}" company-quantity="" readonly
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                          </div>
                            @endforeach
                          @else
                          @php $company_ids = array('1'); @endphp
                          <div class="row " company="1">
                            <div class="col px-0">
                                <div class="container-fluid bg-white">
                                  <div class="row">
                                    <div class="col-lg-12 text-center bg-white p-1">
                                      <input
                                        class="form-control form-control-sm signature-control py-2 text-center px-0" company-type="1" readonly
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col px-0">
                                <div class="container-fluid bg-white">
                                  <div class="row">
                                    <div class="col-lg-12 text-center bg-white p-1">
                                      <input
                                        class="form-control form-control-sm signature-control py-2 text-center px-0" company-brand="1" readonly
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col px-0">
                                <div class="container-fluid bg-white">
                                  <div class="row">
                                    <div class="col-lg-12 text-center bg-white p-1">
                                      <input
                                        class="form-control form-control-sm signature-control py-2 text-center px-0" company-reference="1" readonly
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="col px-0">
                                <div class="container-fluid bg-white">
                                  <div class="row">
                                    <div class="col-lg-12 text-center bg-white p-1">
                                      <input
                                        class="form-control form-control-sm signature-control py-2 text-center px-0" company-quantity="1" readonly
                                      />
                                    </div>
                                  </div>
                                </div>
                              </div>
                        </div>
                        @endif
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-7 ps-0">
                      <div class="container-fluid">
                        <div class="row mb-3">
                          <div class="col-lg-12 ps-0">
                            <h6
                              class="mb-0 administrative-controls fw-bolder fs-13 ps-4 py-2"
                            >
                              Purchase Calendar
                            </h6>
                          </div>
                        </div>
                      </div>
                      <div class="container-fluid">
                        <div class="row p-1 rounded bg-white">
                          <div class="col-lg-5 ps-0 bg-white">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div
                                        class="col-lg-12 px-0 page-col-gray text-center p-1 py-1"

                                      >
                                        <h6
                                          class="mb-0 py-1 page-col-text-header header-ppe"
                                        >
                                          Date of Next Purchase
                                        </h6>
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row ">
                                <div class="col px-0">
                                  <div class="container-fluid bg-white">
                                    <div class="row">
                                      <div
                                        class="col-lg-12 text-center bg-white p-1"
                                      >
                                        <input type="date"
                                          class="form-control form-control-sm signature-control py-2 text-center px-0 date-of-next-purchase date-control" @if(@$operation->ppe == 1) @if(@$operation->date_of_next_purchase)value="{{date("Y-m-d", strtotime($operation->date_of_next_purchase))}}"@endif @endif
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-7  bg-white">
                            <div class="container-fluid pe-0">
                              <div class="row rounded bg-white">
                                <div class="col-lg-12 px-0" id="purchase_calendar">
                                  <!--<div id="sandbox-container">
                                    <div class="date"></div>
                                  </div>-->
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row mt-3 ">
          <div class="col-lg-12 mb-5 text-end">
            <button class="btn app-btn-primary  save fs-13" id="save">
             @lang('app.identification.save')
            </button>
          </div>
        </div>
      </div>
    </main>
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropLabel">@lang('app.manager.engineering_controls') </h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <img
                    src="{{asset('public')}}/media/icons/cancel.png"
                    class="text-xmark" style="background-color: transparent !important;"
                />
              </button>
            </div>
            <div class="modal-body">
              <div class="container">
                <div class="row">
                    <div
                      class="col-md-12 py-2 mb-1"
                    >
                        <div class="form-group" id="engineering-controls">

                      </div>
                    </div>
                  </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal fade" id="staticBackdropAdministrativeControl" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropAdministrativeControlLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="staticBackdropAdministrativeControlLabel">@lang('app.manager.instructions_controls')</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close">
                <img
                    src="{{asset('public')}}/media/icons/cancel.png"
                    class="text-xmark" style="background-color: transparent !important;"
                />
              </button>
            </div>
            <div class="modal-body">
              <div class="container">
                <div class="row">
                    <div
                      class="col-md-12 border border-dark rounded bg-white py-2 mb-1"
                    >
                      <div class="row form-group pb-1 bg-white">
                        <div class="col-10 ps-0 bg-white">
                            <input type="hidden" name="administrative_control_type">
                          <input
                            class="form-control form-control-sm task-control py-0"
                            name="new_administrative_control"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
              </div>
            </div>
            <div class="modal-footer">
                <button
                class="btn app-btn-primary pt-1 pb-1 px-5  save-administrative-control"
              >
                @lang('app.identification.save')
              </button>
            </div>
          </div>
        </div>
      </div>
    <!-- Risk assessment -->
    @php
        //$tasks = json_encode($tasks);
        $hazards = json_encode($hazards);
        $control_ids = json_encode($control_ids);
    @endphp
    <!-- Risk assessment -->
    <script src="{{asset('public')}}/assets/js/bootstrap.bundle.min.js"></script>
    <script src="{{asset('public')}}/assets/js/jquery-3.6.0.min.js"></script>
    <script src="{{asset('public')}}/assets/js/app.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!--<script src="{{asset('public')}}/assets/bootstrap-datepicker/js/bootstrap-datepicker-custom.js"></script>
    -->
    <script>
      var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
      var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
        return new bootstrap.Popover(popoverTriggerEl);
      });
    </script>
    <script>
      /**
       $("#sandbox-container .date").datepicker({
        //startDate: start,
        startView: 0,

      });     **/

      $(document).on('click', '.unwanted-event-details-text', function () {
        if($(this).attr('data') == 1){
            $(this).parent().find('.event-defination').removeClass('border-bottom');
            $(this).parent().parent().find('.details-input-box').fadeOut();
            $(this).attr('data', 0);
            $(this).find('img').css({"transform": "rotate(90deg)"});
        }else{
            $(this).parent().find('.event-defination').addClass('border-bottom');
            $(this).parent().parent().find('.details-input-box').fadeIn();
            $(this).attr('data', 1);
            $(this).find('img').css({"transform": "rotate(270deg)"});
        }
    })

      $(function () {
        const monthNames = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];
        var dateObj = new Date('<?php echo date("Y-m-d"); ?>');
            var month = dateObj.getUTCMonth() + 1; //months from 0-11
            var month_name = monthNames[dateObj.getMonth()];
            var day = dateObj.getUTCDate() + 1;
            var year = dateObj.getUTCFullYear();
            var next_purchase = '<?php echo date("d", strtotime("$operation->date_of_next_purchase")) ?>'
            //var start = "-" + month + "/" + (day) + "/" + year;
            //var today = month + "/" + (day) + "/" + year;
            let calendar = `
            <table class="table table-bordered table-striped mb-0 table-calendar">
                                    <thead class="pb-2">
                                      <tr>
                                        <th colspan="7">${month_name + ' ' + year}</th>
                                      </tr>
                                    </thead>
                                    <tbody>
            `;
          if(month == 1 || month == 3 || month == 5 || month == 7 || month == 8 || month == 10 || month == 12){
            calendar += `<tr>`;
            for(let i=1;i<=7;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=8;i<=14;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=15;i<=21;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=22;i<=28;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=29;i<=31;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `
            <td></td><td></td><td></td><td></td>
            </tr>`;
          }
          if(month == 2){
            calendar += `<tr>`;
            for(let i=1;i<=7;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=8;i<=14;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=15;i<=21;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=22;i<=28;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            if ((0 == year % 4) && (0 != year % 100) || (0 == year % 400)){
                calendar += `</tr> <tr>`;
                  if(i == next_purchase){
                calendar += `<td>29<span class="dot"></span></td>`;
              }else{
                calendar += `<td>29</td>`;
              }
                calendar += `
                <td></td><td></td><td></td><td></td><td></td><td></td>
              </tr>`;
            }else{
              calendar += `</tr> <tr>`;
                calendar += `
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
              </tr>`;
            }

          }
          if(month == 4 || month == 6 || month == 9 || month == 11){
            calendar += `<tr>`;
            for(let i=1;i<=7;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=8;i<=14;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=15;i<=21;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=22;i<=28;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `</tr> <tr>`;
            for(let i=29;i<=30;i++){
              if(i == next_purchase){
                calendar += `<td>${i}<span class="dot"></span></td>`;
              }else{
                calendar += `<td>${i}</td>`;
              }
            }
            calendar += `
            <td></td><td></td><td></td><td></td><td></td>
            </tr>`;
          }
          calendar += `
          </tbody>
          </table>
          `;
          $("#purchase_calendar").html(calendar);
       });
      $(document).on('click', '.page-section', function () {
        if($(this).attr('data-target') == 'general-information'){
            $("#save").removeClass('save');
            $("#save").removeClass('save-conformity');
            $("#save").removeClass('save-ppe');
            $("#save").addClass('save-general-information');
        }
        if($(this).attr('data-target') == 'conformity'){
            $("#save").removeClass('save');
            $("#save").removeClass('save-general-information');
            $("#save").removeClass('save-ppe');
            $("#save").addClass('save-conformity');
        }
        if($(this).attr('data-target') == 'risk-assessment'){
            $("#save").removeClass('save-general-information');
            $("#save").removeClass('save-conformity');
            $("#save").removeClass('save-ppe');
            $("#save").addClass('save');
        }
        if($(this).attr('data-target') == 'ppe'){
            $("#save").removeClass('save-general-information');
            $("#save").removeClass('save-conformity');
            $("#save").removeClass('save');
            $("#save").addClass('save-ppe');
        }
       });
    </script>
    @php
    $risk_assessment_state = $operation->risk_assessment == 0 ? 0 : 1;
    $engineering_controls = [];
    $control_keys = [];
    $custom_controls = [];
    $organizations = [];
    $obligations = [];
    $event_details = [];
    $custom_obligation_controls = [];
    $custom_organization_controls = [];
    if($risk_assessment_state == 1){
      foreach (
        DB::table('administrative_controls as admin_controls')
        ->where('admin_controls.is_deleted', 0)
        ->where('admin_controls.operation_reference', $operation->reference)
        ->select(
            'admin_controls.id',
            'admin_controls.type',
            'c.control as custom_control',
            'p.control as predefined_control',
            'admin_controls.control as control_id',
            'admin_controls.detail',
            'admin_controls.task',
            'admin_controls.operation_reference',
            'admin_controls.hazard',
            'admin_controls.control_type'
        )
        ->leftJoin('custom_administrative_controls as c', function ($join) {
            $join->on('admin_controls.control_type', '=', DB::raw('"custom"'));
            $join->on('admin_controls.control', '=', 'c.id');
            $join->where('c.is_deleted', 0);
        })
        ->leftJoin('predefined_controls as p', function ($join) {
            $join->on('admin_controls.control_type', '=', DB::raw('"predefined"'));
            $join->on('admin_controls.control', '=', 'p.id');
        })
        ->get()
        as
        $value
    ) {
        if($value->type == 'obligation'){

          if($value->control_type == 'custom') {
            $custom = DB::table('custom_administrative_controls')->where('type', 'obligation')->where('id', $value->control_id)->where('is_deleted', 0)->first();
          if(@$custom) {
            array_push($obligations, (object)array(
            "key" => $value->id,
            "control" => $value->control_id,
            "details" => $value->detail,
            "hazard" => $value->hazard,
            "task" => $value->task,
            "control_type" => $value->control_type,
          ));
array_push($custom_obligation_controls, (object) array(
    "id" => $value->id,
    "control" => $custom->control,
    "task" => $custom->task,
    "hazard" => $custom->hazard,
    "type" => $custom->type,
));

          }
            }else {
                array_push($obligations, (object)array(
            "key" => $value->id,
            "control" => $value->control_id,
            "details" => $value->detail,
            "hazard" => $value->hazard,
            "task" => $value->task,
            "control_type" => $value->control_type,
          ));
            }
        }
        if($value->type == 'organization'){
            if($value->control_type == 'custom') {
            $custom = DB::table('custom_administrative_controls')->where('type', 'organization')->where('id', $value->control_id)->where('is_deleted', 0)->first();
          if(@$custom) {
            array_push($organizations, (object)array(
            "key" => $value->id,
            "control" => $value->control_id,
            "details" => $value->detail,
            "hazard" => $value->hazard,
            "task" => $value->task,
            "control_type" => $value->control_type,
          ));
          array_push($custom_organization_controls, (object) array(
    "id" => $value->id,
    "control" => $custom->control,
    "task" => $custom->task,
    "hazard" => $custom->hazard,
    "type" => $custom->type,
));
          }
        }else {
            array_push($organizations, (object)array(
            "key" => $value->id,
            "control" => $value->control_id,
            "details" => $value->detail,
            "hazard" => $value->hazard,
            "task" => $value->task,
            "control_type" => $value->control_type,
          ));
        }

        }
      }
      foreach (
            DB::table('hazard_identification_unwanted_events as hi')
            ->where('hi.is_deleted', 0)
            ->where('hi.is_temp_deleted', 0)
            ->where('hi.operation_reference', $operation->reference)
            ->select(
                "hi.*",
                "ue.event"
            )
            ->join('unwanted_events as ue', 'hi.unwanted_event', '=', 'ue.id')
            ->get()
            as  $unwanted_event
         ){

          array_push($event_details, (object)array(
            "event_id" => $unwanted_event->id,
            "details" => $unwanted_event->hazard_identification_details,
          ));

          foreach (
        DB::table('hazard_identification_engineering_controls as ec')
        ->where('ec.is_deleted', 0)
        ->where('ec.operation_reference', $operation->reference)
        ->where('ec.unwanted_event_ref', $unwanted_event->id)
        ->select(
          "ec.*",
          "c.control as control",
        )
        ->Join("engineering_controls as c", "ec.engineering_control", "=", "c.id")
        ->get() as $engineering_control
      ) {
        if(!in_array($engineering_control->id, $controls)){
          $key = uniqid().''.$engineering_control->id;
          array_push($engineering_controls, (object) array(
            'control-key'.$key => (object)array(
          'task'=> $unwanted_event->task,
          'hazard'=> $unwanted_event->hazard,
          'unwanted_event'=> $unwanted_event->id,
          'control_value'=> $engineering_control->engineering_control,
          'control_name'=> $engineering_control->control,
          'control_details'=> $engineering_control->details,
        )
          ));
          array_push($control_keys, (object)array(
            'id'.$unwanted_event->id => $key,
          ));
        }

      }
         }
    }
    $engineering_controls = json_encode($engineering_controls);
    $control_keys = json_encode($control_keys);
    $custom_controls = json_encode($custom_controls);
    $obligations = json_encode($obligations);
    $organizations = json_encode($organizations);
    $event_details = json_encode($event_details);

    $predefined_obligation_controls = json_encode(DB::table('predefined_controls')->where('type', 'obligation')->get());
    $predefined_organization_controls = json_encode(DB::table('predefined_controls')->where('type', 'organization')->get());
    $custom_obligation_controls = json_encode($custom_obligation_controls);
    $custom_organization_controls = json_encode($custom_organization_controls);
    @endphp
    <script>
        // function to generate a random string of specified length
function generateRandomString(length) {
  let result = '';
  const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
  const charactersLength = characters.length;
  for (let i = 0; i < length; i++) {
    result += characters.charAt(Math.floor(Math.random() * charactersLength));
  }
  return result;
}
      function parseJSONWithUnicode(jsonString) {
  return JSON.parse(jsonString, (key, value) => {
    if (typeof value === 'string') {
      return value.replace(/\\u[\dA-Fa-f]{4}/g, (match) => {
        return String.fromCharCode(parseInt(match.substr(2), 16));
      });
    }
    return value;
  });
}
function jsonEscape(str) {
            return str.replace(/\n/g, "\\\\n").replace(/\r/g, "\\\\r").replace(/\t/g, "\\\\t");
        }
          let tasks = [];
          let hazards = JSON.parse(jsonEscape(`<?php echo $hazards; ?>`)),
          control_ids = JSON.parse(`<?php echo $control_ids; ?>`),
          risk_assessment_status = '<?php echo $risk_assessment_state; ?>';
          engineering_controls = JSON.parse(jsonEscape(`<?php echo $engineering_controls; ?>`)),
          event_details = JSON.parse(jsonEscape(`<?php echo $event_details; ?>`)),
          obligations = JSON.parse(jsonEscape(`<?php echo $obligations; ?>`)),
          organizations = JSON.parse(jsonEscape(`<?php echo $organizations; ?>`)),
          custom_administrative_controls = JSON.parse(jsonEscape(`<?php echo $custom_controls; ?>`)),
          current_hazard = hazards[0],
          current_task = {},
          control_keys = JSON.parse(`<?php echo $control_keys; ?>`),
          predefined_obligations = JSON.parse(jsonEscape(`<?php echo $predefined_obligation_controls ?>`)),
          predefined_organizations = JSON.parse(jsonEscape(`<?php echo $predefined_organization_controls ?>`)),
          custom_obligation_controls = JSON.parse(jsonEscape(`<?php echo $custom_obligation_controls ?>`)),
          custom_organization_controls = JSON.parse(jsonEscape(`<?php echo $custom_organization_controls ?>`)),
          engineering_controls_bubbles = [1, 17 , 30 , 42 , 60 , 106 , 112 , 130 , 139 , 7 , 20 , 37, 64, 74, 93, 105, 108, 140],
          hazard_language = [
                {
                    'Mechanical': `@lang('app.hazard.mechanical')`
                },
        {
            'Electrical': `@lang('app.ihazard.electrical')`
        },
        {
            'Thermal': `@lang('app.hazard.thermal')`
        },{
            'Pressure': `@lang('app.hazard.pressure')`
        },
        {
            'Vibrations': `@lang('app.hazard.vibration')`
        },
        {
            'Power Magnet': `@lang('app.hazard.power_mangnet')`
        },
        {
            'Chemical': `@lang('app.hazard.chemical')`
        },
        {
            'Physical': `@lang('app.hazard.physical')`
        },
        {
            'Biological': `@lang('app.hazard.biological')`
        },
        {
            'Noise': `@lang('app.hazard.noise')`
        },
        {
            'Magnetic Fields': `@lang('app.hazard.magnetic_fields')`
        },
        {
            'Radiations': `@lang('app.hazard.radiations')`
        },
        {
            'Falls': `@lang('app.hazard.falls')`
        },
        {
            'Fall of Objects': `@lang('app.hazard.falls_of_objects')`
        },
        {
            'Ergonomics': `@lang('app.hazard.ergonomics')`
        },
        {
            'Psychological (Mental Health)': `@lang('app.hazard.psychological')`
        },
        {
            'Store Energy': `@lang('app.hazard.stored_energy')`
        },
        {
            'Lone Worker': `@lang('app.hazard.lone_worker')`
        },
        {
            'Environment': `@lang('app.hazard.environment')`
        },
        {
            'Weather': `@lang('app.hazard.weather')`
        }
            ];
          $.ajax({
              type: 'POST',
              url: '{{url("/hazard-manager/load-tasks")}}',
              global: false,
    async:false,
              data: {
                '_token': '{{ csrf_token() }}',
                'id': '{{$operation->reference}}',
              },
            }).done(function (response) {
              tasks = response;
              current_task = tasks[0];
             }).fail(function (response) {
              Swal.fire({
                    icon: 'error',
                    text: 'unable to get tasks data',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
              });
          control_ids.forEach(id => {
            let obj = new Object();
            if(risk_assessment_status == 0){
              obj['control-key'+id] = {
                    'task': current_task.id,
                    'hazard': current_hazard.task_hazard,
                    'unwanted_event': $(`[row-id='${id}']`).attr('data'),
                };
            }else{
              obj['control-key'+id] = {
                    'task': current_task.id,
                    'hazard': current_hazard.task_hazard,
                    'unwanted_event': $(`[row-id='${id}']`).attr('data'),
                    'control_value': $(`[row-id='${id}']`).attr('cvalue'),
                    'control_name': $(`[row-id='${id}']`).attr('cname'),
                    'control_details': $(`[row-id='${id}']`).attr('cdetails'),
                };
            }

            engineering_controls.push(obj);
            let obj2 = new Object();
            obj2['id'+$(`[row-id='${id}']`).attr('data')] = id;
            control_keys.push(obj2);
          });
            $(".task-prev").on('click', function () {
                let sr = parseInt($(".task-sr").attr('sr'));
                if(sr > 0){
                    let prev = sr-1;
                    current_task = tasks[prev];
                    let name = tasks[prev].task;
                    $(".task-name").text(name);
                    $(".task-sr").text(`@lang("app.manager.task") ${(parseInt(prev) + 1)}`);
                    $(".task-sr").attr('sr', prev);
                    task_hazards(tasks[prev].operation_reference, tasks[prev].id);
                }
             });
            $(".task-next").on('click', function () {
                let sr = parseInt($(".task-sr").attr('sr'));
                if(sr < tasks.length-1){
                    let next = sr+1;
                    current_task = tasks[next];
                    let name = tasks[next].task;
                    $(".task-name").text(name);
                    $(".task-sr").text(`@lang("app.manager.task") ${(parseInt(next) + 1)}`);
                    $(".task-sr").attr('sr', next);
                    task_hazards(tasks[next].operation_reference, tasks[next].id);
                }
            })
            $(".hazard-prev").on('click', function () {
                let sr = parseInt($(".hazard-name").attr('sr'));
                if(sr > 0){
                    let prev = sr-1;
                    current_hazard = hazards[prev];
                    let name = hazards[prev].task_hazard;
                    let lan_name = name;
                    hazard_language.forEach(l => {
                        if(name in l){
                            lan_name = l[name];
                        }
                    })
                    $(".hazard-name").text(lan_name);
                    $(".hazard-name").attr('sr', prev);
                    hazard_unwanted_events(current_task.operation_reference, current_task.id, hazards[prev].task_hazard);
                    hazard_instructions(current_task.operation_reference, current_task.id, hazards[prev].task_hazard);
                  }
            });
            $(".hazard-next").on('click', function () {
                let sr = parseInt($(".hazard-name").attr('sr'));
                if(sr < hazards.length - 1){
                    let next = sr+1;
                    current_hazard = hazards[next];
                    let name = hazards[next].task_hazard;
                    let lan_name = name;
                    hazard_language.forEach(l => {
                        if(name in l){
                            lan_name = l[name];
                        }
                    })
                    $(".hazard-name").text(lan_name);
                    $(".hazard-name").attr('sr', next);
                    hazard_unwanted_events(current_task.operation_reference, current_task.id, hazards[next].task_hazard);
                    hazard_instructions(current_task.operation_reference, current_task.id, hazards[next].task_hazard);
                }
            });
            $(document).on('click', ".select-engineering-event", function(){
                var $this   = $(this);
                let unwanted_event = $this.attr('unwanted_event_id');
                let id = $this.attr('hazard-unwanted-event-id');
                let control = $this.attr('row-id');
                if($this.attr('state') == 1){
                    $this.attr('state', 0);
                    var popovers = $('[data-bs-toggle*="popover-control"]');
                popovers.each(function (index) {
                    $(this).popover('hide');
                 })
                    $(`[r_id='${control}']`).html('');
                }else{
                    $.ajax({
                    type: 'GET',
                    url: '{{url("/hazard-manager/unwanted-event/engineering-controls")}}',
                    data: {
                        //'hazard': current_hazard.task_hazard,
                        'unwanted_event': unwanted_event,
                    },
                }).done((response)=>{
                    if(response.length > 0){
                        let html = ``;
                        let i = 1;
                        response.forEach(element => {
                            html += `
                            <div class="form-check form-check-inline ps-0 me-0 pe-0 mt-2">
                          <input class="btn-check" type="radio" control_id="${control}" value="${element.id}" data="${element.control}" name="engineering-controls" id="${element.control}${i}" `
                          if(i == 1){
                            //html += `checked=""`
                          }
                          html +=`>
                          <label class="btn btn-outline-primary w-100 hstack" for="${element.control}${i}">
                            ${element.control} `;
                            html+=`
                            <img `;
                            if(i == 1){
                              html+=`src="{{asset('public')}}/media/icons/info-bubble-blue.png"`;//html+=`src="{{asset('public')}}/media/icons/info-bubble-gray.png"`;
                            }else{
                              html+=`src="{{asset('public')}}/media/icons/info-bubble-blue.png"`;
                            }
                            html+=`class="ms-auto `;
                            if(i == 1){
                              html+= `bg-white`;//html += `app-primary-bg`
                          } else{
                            html+= `bg-white`;
                          }
                            html+=` info" style="width: 14px;" data-bs-toggle="popover-control" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  <div class='popover-header'>
                    ${element.control}
                    </div>
                  <div class='popover-container container-fluid'>
                    ${element.info}
                    </div>
                  "
                  >
                            `;
                          html+=`</label>
                        </div>
                            `;
                            i++;
                        });
                        $(`[r_id='${control}']`).html(html);
                    }
                    var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover-control"]'))
                    var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
                        return new bootstrap.Popover(popoverTriggerEl, {
                            template:'<div class="popover engineering-control-popover" role="tooltip"><div class="popover-arrow"></div><h3 class="popover-header"></h3><div class="popover-body"></div></div>'
                        });
                    });
                    $this.attr('state', 1);
                }).fail((response)=>{
                  //console.log(response)
                    Swal.fire({
                    icon: 'error',
                    text: 'Unable to get engineering controls.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
                });
                }
            });
            $("body").on('click', ".plus-engineering-control", function(){
                let unwanted_event_id = $(this).attr('unwanted_event_id');
                let data = $(this).attr('data');
                let key = uniqid(data);
                html = `
                <div class="row" control-id="${key}">
                    <div class="col-lg-4 px-0">
                        <div class="container-fluid">
                            <div class="row">
                                <div
                                    class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="${key}" data="${data}" unwanted_event_id="${unwanted_event_id}"
                                >
                                <h6 class="mb-0 py-1 text-dark bg-white py-1">
                                                                        @lang('app.manager.select')
                                                                    </h6>
                                </div>
                            </div>
                            <div class="row" r_id="${key}" e_id="${data}" ue_id="${unwanted_event_id}">

                                                                </div>
                        </div>
                    </div>
                <div class="col-lg-8 pe-0 ps-1">
                    <div class="container-fluid">
                        <div class="row">
                            <div
                                class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                            >
                                <div class="container bg-white">
                                    <div class="row">
                                        <div class="col-md-12 bg-white pe-0">
                                            <div class="container-fluid">
                                                <div class="row">
                                                    <div class="col-10 bg-white">
                                                        <input
                                                            class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details" control-row-id="${key}" data="${data}"
                                                            id="event-detail"
                                                        />
                                                    </div>
                                                <div class="col-2 bg-white pe-0">
                                                    <a
                                                        class="bg-white text-decoration-none remove-strategy-of-controls" ref-id="${key}" data="${data}"
                                                        href="javascript:;"
                                                    >
                                                        <img
                                                            src="{{asset('public')}}/media/icons/cancel.png"
                                                            class="text-xmark"
                                                        />
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div
                            class="col-lg-1 pe-0 text-center mt-2"
                        >
                                <div class="btn btn-sm app-btn-selection-outline">
                                    <img
                                         src="{{asset('public')}}/media/icons/plus-solid.svg"
                                  class="btn-xmark plus-engineering-control bg-white" unwanted_event_id="${unwanted_event_id}" data="${data}"
                                             />
                                    </div>
                                 </div>
                             </div>
                         </div>
                    </div>
                </div>
                `;
                $(`[engineering-control='${data}']`).append(html);
                let obj = new Object();
                obj['control-key'+key] = {
                    'task': current_task.id,
                    'hazard': current_hazard.task_hazard,
                    'unwanted_event': data,
                };
                engineering_controls.push(obj);
                let obj2 = new Object();
                obj2['id'+data] = key;
                control_keys.push(obj2);
            });
            $(document).on('change', 'input[name=engineering-controls]', function () {
                let control_id = $(this).attr('control_id');
                let engineering_controls_selected = $(this).attr('data');
                let control_value = '';
                if($(this).is(':checked')){
                  control_value = $(this).val();
                }
                if(control_id != '' && engineering_controls_selected != '' && control_value != ''){
                    let obj = new Object();
                    let control_to_remove = [];
                    for(let i=0;i<engineering_controls.length;i++){
                        if(engineering_controls[i]['control-key'+control_id]){
                            obj['control-key'+control_id] = {'hazard': engineering_controls[i]['control-key'+control_id].hazard, 'task': engineering_controls[i]['control-key'+control_id].task, 'unwanted_event': engineering_controls[i]['control-key'+control_id].unwanted_event, 'control_value': control_value, 'control_name': engineering_controls_selected};
                            control_to_remove.push(i);
                        }
                    }
                    control_to_remove.sort(function(a,b){ return b - a; });
                    for (var i = 0; i <= control_to_remove.length -1; i++)
                        engineering_controls.splice(control_to_remove[i],1);
                    engineering_controls.push(obj);
                    $(`[row-id='${control_id}']`).html(`<h6 class="mb-0 py-1 text-dark bg-white py-1">${engineering_controls_selected}</h6>`);
                    //console.log(engineering_controls);
                }
                var popovers = $('[data-bs-toggle*="popover-control"]');
                popovers.each(function (index) {
                    $(this).popover('hide');
                 })
                $(`[r_id='${control_id}']`).html('');
                save_risk_assessment();
            });
            $(document).on('change', '.strategy-of-control-details', function () {
                let value = $(this).val();
                let control_id = $(this).attr('control-row-id');
                if(value != ''){
                    let obj = new Object();
                    let control_to_remove = [];
                    for(let i=0;i<engineering_controls.length;i++){
                        if(engineering_controls[i]['control-key'+control_id]){
                            obj['control-key'+control_id] = {'hazard': engineering_controls[i]['control-key'+control_id].hazard, 'task': engineering_controls[i]['control-key'+control_id].task, 'unwanted_event': engineering_controls[i]['control-key'+control_id].unwanted_event, 'control_value': engineering_controls[i]['control-key'+control_id].control_value, 'control_name': engineering_controls[i]['control-key'+control_id].control_name, 'control_details': value};
                            control_to_remove.push(i);
                        }
                    }
                    control_to_remove.sort(function(a,b){ return b - a; });
                    for (var i = 0; i <= control_to_remove.length -1; i++)
                        engineering_controls.splice(control_to_remove[i],1);
                    engineering_controls.push(obj);
                    save_risk_assessment();
                }
            });
            $("body").on('click', '.remove-strategy-of-controls', function(){
                let control = $(this).attr('ref-id');
                let id = $(this).attr('data');
                let control_to_remove = [];
                for(let i=0;i<engineering_controls.length;i++){
                    if(engineering_controls[i]['control-key'+control]){
                        control_to_remove.push(i);
                    }
                }
                control_to_remove.sort(function(a,b){ return b - a; });
                for (var i = 0; i <= control_to_remove.length -1; i++)
                    engineering_controls.splice(control_to_remove[i],1);
                let key_to_remove = [];
                for(let i=0;i<control_keys.length;i++){
                    if(control_keys[i]['id'+id] == control){
                        key_to_remove.push(i);
                    }
                }
                for (var i = 0; i <= key_to_remove.length -1; i++)
                    control_keys.splice(key_to_remove[i],1);
                $(`[control-id="${control}"]`).remove();
                save_risk_assessment();
            });
            $("body").on('change', '.unwanted-event-details', function () {
                let details = $(this).val();
                let event_id = $(this).attr('event-id');
                if(details){
                    let events_to_remove = [];
                    for(let i=0;i<event_details.length;i++){
                        if(event_details[i].event_id == event_id){
                            events_to_remove.push(i);
                        }
                    }
                    events_to_remove.sort(function(a,b){ return b - a; });
                    for (var i = 0; i <= events_to_remove.length -1; i++)
                        event_details.splice(events_to_remove[i],1);
                    event_details.push({
                        'event_id': event_id,
                        'details': details,
                    });
                    save_risk_assessment();
                }
            });
            $(document).on('click', ".add-new-obligation", function(){
                $("input[name=administrative_control_type]").val('obligation');
                $("#staticBackdropAdministrativeControl").modal('show');
            });
            $(document).on('click', ".add-new-organization", function(){
                $("input[name=administrative_control_type]").val('organization');
                $("#staticBackdropAdministrativeControl").modal('show');
            })
            $(".save-administrative-control").on('click', function(){
                const type = $("input[name=administrative_control_type]").val();
                const control = $("input[name=new_administrative_control]").val();
                if(type == 'obligation' && control != ''){
                    $("#obligationsContainer").append(`
                <div class="col-lg-4 px-0 mt-1 page-col text-center p-1 obligation d-none administrative-control-selected" obligation="${control}" active="1">
                    <h6 class="mb-0 py-1 page-col-text bg-white administrative-control-selected">
                        ${control}
                    </h6>
                </div>
                `);
                let key = uniqid(control + '-' + current_task.id + '-' + generateRandomString(10));
                custom_obligation_controls.push({
                    "control": control,
                    "type": type,
                    "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "id": key,
                });

                //add details field
                    obligations.push({
                      "control": control,
                    "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "key": key,
                      "control_type": "custom",
                    });
                    administrative_controls_details(key, key, 'custom', 'obligation', current_hazard.task_hazard, current_task.id, '');

                }
                if(type == 'organization' && control != ''){
                    $("#organizationsContainer").prepend(`
                <div class="col-lg-4 px-0 mt-1 page-col text-center p-1 organization d-none administrative-control-selected" organization="${control}" active="1">
                    <h6 class="mb-0 py-1 page-col-text bg-white administrative-control-selected">
                        ${control}
                    </h6>
                </div>
                `);
                let key = uniqid(control + '-' + current_task.id + '-' + generateRandomString(10));
                custom_organization_controls.push({
                    "control": control,
                    "type": type,
                    "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "id": key,
                });
                //
                organizations.push({
                  "control": control,
                    "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "key": key,
                      "control_type": "custom",
                });
                    administrative_controls_details(key, key, 'custom', 'organization', current_hazard.task_hazard, current_task.id, '');
                }
                $("#staticBackdropAdministrativeControl").modal('hide');
                $("input[name=administrative_control_type]").val('');
                $("input[name=new_administrative_control]").val('');
                save_risk_assessment();
            });
            $("body").on('click', '.obligation', function(){
              if($(this).attr('active') == 1){

                }else{
                    $(this).addClass("administrative-control-selected");
                    $(this).children().addClass("administrative-control-selected");
                    $(this).find('img').attr('src', "{{asset('public')}}/media/icons/info-bubble-gray.png");
                    $(this).attr('active', 1);
                    //obligations.push($(this).attr('obligation'));
                    let key = uniqid($(this).attr('obligation') + '-' + current_task.id + '-' + generateRandomString(10));
                    obligations.push({
                      "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "control": $(this).attr('obligation'),
                      "key": key,
                      "control_type": "predefined",
                    });
                    administrative_controls_details($(this).attr('obligation'), key, 'predefined','obligation', current_hazard.task_hazard, current_task.id, '');
                    save_risk_assessment();
                  }
            });
            $("body").on('click', '.organization', function(){
                if($(this).attr('active') == 1){

                }else{
                    $(this).addClass("administrative-control-selected");
                    $(this).children().addClass("administrative-control-selected");
                    $(this).find('img').attr('src', "{{asset('public')}}/media/icons/info-bubble-gray.png");
                    $(this).attr('active', 1);
                    //organizations.push($(this).attr('organization'));
                    let key = uniqid($(this).attr('organization') + '-' + current_task.id + '-' + generateRandomString(10));
                    organizations.push({
                      "hazard": current_hazard.task_hazard,
                      "task": current_task.id,
                      "control": $(this).attr('organization'),
                      "key": key,
                      "control_type": "predefined"
                    });
                    administrative_controls_details($(this).attr('organization'), key, 'predefined','organization', current_hazard.task_hazard, current_task.id, '');
                    save_risk_assessment();
                  }
            });
            $(document).on('click', '.delete-administrative-control', function () {
                let key = $(this).attr('control-key');
                let control = $(this).attr('control');
                let control_type = $(this).attr('control-type');
                let task = $(this).attr('task-d');
                let hazard = $(this).attr('hazard-d');
                let db_type = $(this).attr('db-type');
                if(control_type == 'obligation'){
                    if(db_type == 'predefined') {
                        $(`[obligation='${control}']`).removeClass("administrative-control-selected");
                    $(`[obligation='${control}']`).children().removeClass("administrative-control-selected");
                    $(`[obligation='${control}']`).find('img').attr('src', "{{asset('public')}}/media/icons/info-bubble-blue.png");
                    $(`[obligation='${control}']`).attr('active', 0);
                    }

                    if(obligations.length > 0){
                      let obligations_to_remove = [];
                        for(let i=0;i<obligations.length;i++){
                          if(obligations[i]['task'] == task && obligations[i]['hazard'] == hazard && obligations[i]['key'] == key){
                            obligations_to_remove.push(i);
                          }
                        }
                        obligations_to_remove.sort(function(a,b){ return b - a; });
                        for (var i = 0; i <= obligations_to_remove.length -1; i++)
                          obligations.splice(obligations_to_remove[i],1);
                        $(".obligation-details").each(function () {
                          if($(this).attr('field') == key && $(this).attr('type') == control_type && $(this).attr('task-i') == task && $(this).attr('hazard-i') == hazard){
                            $(this).parent().parent().parent().parent().remove();
                          }
                        });
                    }
                    if(db_type == 'custom' && custom_obligation_controls.length > 0) {
                        let custom_obligations_to_remove = [];
                        for(let i=0;i<custom_obligation_controls.length;i++){
                          if(custom_obligation_controls[i]['task'] == task && custom_obligation_controls[i]['hazard'] == hazard && custom_obligation_controls[i]['id'] == key){
                            custom_obligations_to_remove.push(i);
                          }
                        }
                        custom_obligations_to_remove.sort(function(a,b){ return b - a; });
                        for (var i = 0; i <= custom_obligations_to_remove.length -1; i++)
                          custom_obligation_controls.splice(custom_obligations_to_remove[i],1);
                        }
                }
                if(control_type == 'organization'){
                    if(db_type == 'predefined')
                    {
                        $(`[organization='${control}']`).removeClass("administrative-control-selected");
                    $(`[organization='${control}']`).children().removeClass("administrative-control-selected");
                    $(`[obligation='${control}']`).find('img').attr('src', "{{asset('public')}}/media/icons/info-bubble-blue.png");
                    $(`[organization='${control}']`).attr('active', 0);
                    }

                    if(organizations.length > 0){
                      let organizations_to_remove = [];
                        for(let i=0;i<organizations.length;i++){
                          if(organizations[i]['task'] == task && organizations[i]['hazard'] == hazard && organizations[i]['key'] == key){
                            organizations_to_remove.push(i);
                          }
                        }
                        organizations_to_remove.sort(function(a,b){ return b - a; });
                        for (var i = 0; i <= organizations_to_remove.length -1; i++)
                          organizations.splice(organizations_to_remove[i],1);
                      $(".organization-details").each(function () {
                        if($(this).attr('field') == key && $(this).attr('type') == control_type && $(this).attr('task-i') == task && $(this).attr('hazard-i') == hazard){
                          $(this).parent().parent().parent().parent().remove();
                        }
                      });
                    }

                    if(db_type == 'custom' && custom_organization_controls.length > 0) {
                        let custom_organizations_to_remove = [];
                        for(let i=0;i<custom_organization_controls.length;i++){
                          if(custom_organization_controls[i]['task'] == task && custom_organization_controls[i]['hazard'] == hazard && custom_organization_controls[i]['id'] == key){
                            custom_organizations_to_remove.push(i);
                          }
                        }
                        custom_organizations_to_remove.sort(function(a,b){ return b - a; });
                        for (var i = 0; i <= custom_organizations_to_remove.length -1; i++)
                          custom_organization_controls.splice(custom_organizations_to_remove[i],1);
                        }
                }
                save_risk_assessment();
                //$(this).parent().parent().parent().parent().remove();
            });
            $(document).on('change', '.obligation-details', function () {
              const details = $(this).val();
              const control = $(this).attr('field');//basically key
              const type = $(this).attr('type');
              const task = $(this).attr('task-i');
              const hazard = $(this).attr('hazard-i');
              if(details != ''){
                let ob = new Object();
                obligations.forEach(element => {
                  if(element['task'] == task && element['hazard'] == hazard && element['key'] == control){
                    ob['task'] = element['task'];
                    ob['hazard'] = element['hazard'];
                    ob['control'] = element['control'];
                    ob['details'] = details;
                    ob['control_type'] = element['control_type'];
                    ob['key'] = element['key'];
                    return;
                  }
                });
                let obligations_to_remove = [];
                for(let i=0;i<obligations.length;i++){
                  if(obligations[i]['task'] == task && obligations[i]['hazard'] == hazard && obligations[i]['key'] == control){
                    obligations_to_remove.push(i);
                  }
                }
                obligations_to_remove.sort(function(a,b){ return b - a; });
                for (var i = 0; i <= obligations_to_remove.length -1; i++)
                  obligations.splice(obligations_to_remove[i],1);
                obligations.push(ob);
              }
              save_risk_assessment();
             });
             $(document).on('change', '.organization-details', function () {
              const details = $(this).val();
              const control = $(this).attr('field');//basically key
              const type = $(this).attr('type');
              const task = $(this).attr('task-i');
              const hazard = $(this).attr('hazard-i');
              if(details != ''){
                let ob = new Object();
                organizations.forEach(element => {
                  if(element['task'] == task && element['hazard'] == hazard && element['key'] == control){
                    ob['task'] = element['task'];
                    ob['hazard'] = element['hazard'];
                    ob['control'] = element['control'];
                    ob['details'] = details;
                    ob['control_type'] = element['control_type'];
                    ob['key'] = element['key'];
                    return;
                  }
                });
                let organizations_to_remove = [];
                for(let i=0;i<organizations.length;i++){
                  if(organizations[i]['task'] == task && organizations[i]['hazard'] == hazard && organizations[i]['key'] == control){
                    organizations_to_remove.push(i);
                  }
                }
                organizations_to_remove.sort(function(a,b){ return b - a; });
                for (var i = 0; i <= organizations_to_remove.length -1; i++)
                  organizations.splice(organizations_to_remove[i],1);
                organizations.push(ob);
              }
              save_risk_assessment();
             });
            $(document).on('click', ".save",function(){
                save_risk_assessment();
                setTimeout(function(){
                  window.location.href = '{{url("/home")}}'
                });
            });
            /**
             * methods
            */
           const save_risk_assessment = () => {
            $.ajax({
                    type: 'POST',
                    url: '{{url("/hazard-manager/risk-assessment/save")}}',
                    data: {
                        '_token': '{{ csrf_token() }}',
                        'operation_reference': '{{ $operation->reference }}',
                        'keys': control_keys,
                        'event_details': event_details,
                        'engineering_controls': engineering_controls,
                        'obligation_details': obligations,
                        'organization_details': organizations,
                        'custom_obligation_controls': custom_obligation_controls,
                        'custom_organization_controls': custom_organization_controls,
                    },
                }).done((response)=>{
                  return;
                }).fail((response)=>{
                    Swal.fire({
                    icon: 'error',
                    text: 'Unable to save Risk Assessment.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
                });
           }
           //get task hazards
           let task_hazards = (reference, task) => {
                $.ajax({
                    type: 'GET',
                    url: '{{url("/hazard-manager/task/hazards")}}',
                    data: {
                        'reference': reference,
                        'task': task
                    },
                }).done((response) => {
                    hazards = response;
                    current_hazard = hazards[0];
                    hazard_unwanted_events(current_task.operation_reference, current_task.id, current_hazard.task_hazard);
                    let lan_name = hazards[0].task_hazard;
                    hazard_language.forEach(l => {
                        if(hazards[0].task_hazard in l){
                            lan_name = l[hazards[0].task_hazard];
                        }
                    })
                    $(".hazard-name").text(lan_name);
                    $(".hazard-name").attr('sr', 0);
                    hazard_instructions(current_task.operation_reference, current_task.id, hazards[0].task_hazard);
                }).fail((response) => {
                    Swal.fire({
                    icon: 'error',
                    text: 'Unable to get task hazards.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
                });
           };
           const hazard_instructions = (reference, task, hazard) => {
          let organization_language = [
            {
                '9' : `@lang('app.organization.human_support')`
            },
            {
                '10': `@lang('app.organization.worker_rotation')`
            },
            {
                '11': `@lang('app.organization.work_breaks')`
            },
            {
                '12': `@lang('app.organization.access_control')`
            },
            {
                '13': `@lang('app.organization.cleaning')`
            },
            {
                '14': `@lang('app.organization.monitering')`
            },
            {
                '15': `@lang('app.organization.workplace_layout')`
            },
            {
                '16': `@lang('app.organization.hydration')`
            }
          ]
          let obligation_language = [
                {
                    '1': `@lang('app.obligations.point_of_operation')`
                },
                {
                    '2': `@lang('app.obligations.speed_limit')`
                },
                {
                    '3': `@lang('app.obligations.safety_distance')`
                },
                {
                    '4': `@lang('app.obligations.body_part_position')`
                },
                {
                    '5': `@lang('app.obligations.weight_limit')`
                },
                {
                    '6': `@lang('app.obligations_height_limit')`
                },
                {
                    '7': `@lang('app.obligations.step')`
                },
                {
                    '8': `@lang('app.obligations.time_limit')`
                }
            ];
            $("#administrative-controls-details").html('');
              //obligations
              $("#obligationsContainer").html('');
              let obligations_html = ``;

              custom_obligation_controls.forEach(control => {
                obligations.forEach(element => {
                    if(element['control_type'] == 'custom')
                    {
                        if(element['task'] == task && element['hazard'] == hazard && element['key'] == control.id){
                            administrative_controls_details(control.id, element['key'], 'custom', 'obligation', hazard, task, element['details'] != undefined ? element['details'] : '');
                        }
                    }
                });
              });
              predefined_obligations.forEach(control => {
                let check = 0;
                obligations.forEach(element => {
                    if(element['control_type'] == 'predefined') {
                        if(element['task'] == task && element['hazard'] == hazard && element['control'] == control.id){
                    obligations_html+= `
                      <div class="col-lg-4 px-0 mt-1 " >
                        <div class="page-col text-center p-1 obligation administrative-control-selected hstack" obligation="${control.id}" active="1">
                          <h6 class="mb-0 py-1 w-100 page-col-text bg-white administrative-control-selected">`
                            obligation_language.forEach(l => {
                                if( control.id in l){
                                    obligations_html+=`${l[control.id]}`;
                                }
                            })
                          obligations_html += `</h6>`;
                          if(control.id == '1'){
                            obligations_html+=`
                            <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                            data-bs-content="
                              @lang('app.bubble.manager.point_of_operation')
                            "
                            >
                            `;
                          }
                          if(control.id == '4'){
                            obligations_html += `
                              <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                                data-bs-content="
                                  @lang('app.bubble.manager.body_part_position')
                                "
                                >
                                `;
                          }
                          if(control.id == '7'){
                            obligations_html += `
                              <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                                data-bs-content="
                                  @lang('app.bubble.manager.body.step')
                                "
                              >
                              `;
                            }
                            obligations_html+=`</div>
                          </div>
                          `;
                          administrative_controls_details(control.id, element['key'], 'predefined', 'obligation', hazard, task, element['details'] != undefined ? element['details'] : '');
                          check = 1;
                          }
                    }
                });
                if(check == 0){
                  obligations_html+= `
                <div class="col-lg-4 px-0  mt-1" >
                  <div class=" page-col text-center p-1 obligation hstack" obligation="${control.id}" active="0">
                  <h6 class="mb-0 py-1 page-col-text bg-white w-100">`
                        obligation_language.forEach(l => {
                            if(control.id in l){
                                obligations_html += `${l[control.id]}`;
                            }
                        })
                    obligations_html +=`</h6>`;
                    if(control.id == '1'){
                      obligations_html+=`
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.point_of_operation')
                  "
                  >
                      `;
                    }
                    if(control.id == '4'){
                      obligations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.body_part_position')
                  "
                  >
                      `;
                    }
                    if(control.id == '7'){
                      obligations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.body.step')
                  "
                  >
                      `;
                    }
                    obligations_html+=`</div>
                </div>
                `;
                }
              });
              obligations_html += `
              <div class="col-lg-4 px-0 mt-1 text-center">
                                  <button
                                    class="btn btn-yellow   w-100 add-new-obligation"

                                  >
                                    @lang('app.manager.add_new')
                                  </button>
                                </div>
              `;
              $("#obligationsContainer").html(obligations_html);
              //organizations
              $("#organizationsContainer").html('');
              let organizations_html = ``;
              custom_organization_controls.forEach(control => {
                organizations.forEach(element => {
                  if(element['control_type'] == 'custom') {
                    if(element['task'] == task && element['hazard'] == hazard && element['key'] == control.id){

administrative_controls_details(control.id, element['key'], 'custom', 'organization', hazard, task, element['details'] != undefined ? element['details'] : '');

  }
                  }
                });
              });
              predefined_organizations.forEach(control => {
                let check = 0;
                organizations.forEach(element => {
                  if(element['control_type'] == 'predefined') {
                    if(element['task'] == task && element['hazard'] == hazard && element['control'] == control.id){
                    organizations_html+= `
                <div class="col-lg-4 px-0 mt-1 " >
                  <div class="page-col text-center p-1 organization administrative-control-selected hstack" organization="${control.id}" active="1">
                    <h6 class="mb-0 py-1 page-col-text bg-white administrative-control-selected w-100">
                        `
                        organization_language.forEach(l => {
                            if(control.id in l){
                                organizations_html += `${l[control.id]}`
                            }
                        })
                    organizations_html +=`
                    </h6>`;
                    if(control.id == '9'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.human_support')
                  "
                  >
                      `;
                    }
                    if(control.id == '14'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                        @lang('app.bubble.manager.monitering')
                  "
                  >
                      `;
                    }
                    if(control.id == '15'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-gray.png" class="ms-3 info administrative-control-selected" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.workplace_layout')
                  "
                  >
                      `;
                    }
                    organizations_html+=`</div>
                </div>
                `;
                administrative_controls_details(control.id, element['key'], 'predefined', 'organization', hazard, task, element['details'] != undefined ? element['details'] : '');
                check = 1;
                  }
                  }
                });
                if(check == 0){
                  organizations_html+= `
                <div class="col-lg-4 px-0 mt-1 " >
                  <div class="page-col text-center p-1 organization hstack" organization="${control.id}" active="0">
                  <h6 class="mb-0 py-1 page-col-text bg-white w-100">
                    `
                        organization_language.forEach(l => {
                            if(control.id in l){
                                organizations_html += `${l[control.id]}`
                            }
                        })
                    organizations_html +=`
                    </h6>`;
                    if(control.id == '9'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.human_support')
                  "
                  >
                      `;
                    }
                    if(control.id == '14'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                  @lang('app.bubble.manager.monitering')
                  "
                  >
                      `;
                    }
                    if(control.id == '15'){
                      organizations_html += `
                      <img src="{{asset('public')}}/media/icons/info-bubble-blue.png" class="ms-3 info bg-white" style="width: 14px;" data-bs-toggle="popover" data-bs-trigger="hover focus" data-bs-html="true"
                  data-bs-content="
                 @lang('app.bubble.manager.workplace_layout')
                  "
                  >
                      `;
                    }
                    organizations_html+=`</div>
                </div>
                `;
                }
              });
              organizations_html += `
              <div class="col-lg-4 px-0 mt-1 ">
                                  <button
                                    class="btn btn-yellow pt  w-100 add-new-organization"

                                  >
                                    @lang('app.manager.add_new')
                                  </button>
                                </div>
              `;
              $("#organizationsContainer").html(organizations_html);
              var popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'))
var popoverList = popoverTriggerList.map(function (popoverTriggerEl) {
  return new bootstrap.Popover(popoverTriggerEl);
});
           };

           //unwanted events
           let hazard_unwanted_events = (reference, task, hazard) => {
                $("#unwanted_events").html('');
                 $.ajax({
                    type: 'GET',
                    url: '{{url("/hazard-manager/task/hazard/unwanted-events")}}',
                    data: {
                        'reference': reference,
                        'task': task,
                        'hazard': hazard
                    },
                }).done((response) => {
                    if(response.length > 0){
                        let html = ``;
                        response.forEach(element => {
                            let key = uniqid(element.id);
                            let detail_value = '';
                            if(element.hazard_identification_details != null && element.hazard_identification_details != '' && element.hazard_identification_details != undefined){
                              detail_value = element.hazard_identification_details;

                            }
                            for(let i=0;i<event_details.length;i++){
                                if(event_details[i].event_id == element.id){
                                  if(event_details[i].details != null && event_details[i].details != '' && event_details[i].details != undefined){
                                    detail_value = event_details[i].details;
                                  }

                                }
                            }
                            let keys = [];
                            for(let i=0;i<control_keys.length;i++){
                                if(control_keys[i]['id'+element.id]){
                                    keys.push(control_keys[i]['id'+element.id]);
                                }
                            }
                            html += `

                            <div class="row" hazard-type="${element.hazard}" event-row="${element.id}">
                                <div class="col-lg-7 px-0">
                                                <div class="container-fluid">
                                                    <div class="row">
                                                        <div
                                                            class="col-lg-12 px-0 page-col text-center p-1 mt-2"
                                                        >
                                                            <div class="hstack">
                                                                <h6 class="mb-0 py-1 text-dark bg-white px-1 w-100 event-defination">
                                                                    ${element.event}
                                                                </h6>
                                                                <div class="unwanted-event-details-text px-2 border-start cursor-pointer">
                                                                    @lang('app.manager.details')
                                                                    <img src="{{asset('public/media/icons/pointer.svg')}}" class="pointer">
                                                                </div>
                                                            </div>
                                                            <div class="px-5 details-input-box" style="display: none;">
                                                                <input
                                                                    class="form-control form-control-sm task-control py-0 px-0 unwanted-event-details" value="${detail_value}" event-id="${element.id}"
                                                                />
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="col-lg-5 pe-0 ps-3">
                                                <div class="container-fluid" engineering-control="${element.id}">`;
                                                    if(keys.length > 0){
                                                        keys.forEach(k => {
                                                            let control = {};
                                                            engineering_controls.forEach(con => {
                                                                if(con['control-key'+k]){
                                                                    control = con['control-key'+k];
                                                                }
                                                            });
                                                            //console.log(control)
                                                            if(!$.isEmptyObject(control)){
                                                            html+=`<div class="row" control-id="${k}">
                                                        <div class="col-lg-4 px-0">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="${k}" data="${element.id}" unwanted_event_id="${element.unwanted_event}"
                                                                    >
                                                                        <h6 class="mb-0 py-1 text-dark bg-white py-1">`
                                                                            if(control.control_name){
                                                                                    html+= `${control.control_name}`;
                                                                            }else{
                                                                                html += `Select`
                                                                            }

                                                                        html +=`</h6>
                                                                    </div>
                                                                </div>
                                                                <div class="row" r_id="${k}" e_id="${element.id}" ue_id="${element.unwanted_event}">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 pe-0 ps-1">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                                                                    >
                                                                        <div class="container bg-white">
                                                                            <div class="row">
                                                                                <div class="col-md-12 bg-white pe-0">
                                                                                    <div class="container-fluid">
                                                                                        <div class="row">
                                                                                            <div class="col-10 bg-white">
                                                                                                <input
                                                                                                    class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details"`;
                                                                                                    if(control.control_details){
                                                                                                        html+=`value="${control.control_details}"`;
                                                                                                    }
                                                                                                    html+=`control-row-id="${k}" data="${element.id}"
                                                                                                    id="event-detail"
                                                                                                />
                                                                                            </div>
                                                                                            <div class="col-2 bg-white pe-0">
                                                                                                <a
                                                                                                    class="bg-white text-decoration-none remove-strategy-of-controls" ref-id="${k}" data="${element.id}"
                                                                                                    href="javascript:;"
                                                                                                >
                                                                                                    <img
                                                                                                        src="{{asset('public')}}/media/icons/cancel.png"
                                                                                                        class="text-xmark"
                                                                                                    />
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="col-lg-1 pe-0 text-center mt-2"
                                                                    >
                                                                        <div class="btn btn-sm app-btn-selection-outline">
                                                                            <img
                                                                            src="{{asset('public')}}/media/icons/plus-solid.svg"
                                                                            class="btn-xmark plus-engineering-control" unwanted_event_id="${element.unwanted_event}" data="${element.id}"
                                                                            />
                                                                            </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`;
                                                }
                                                        });
                                                    }else{
                                                        html+=`<div class="row" control-id="${key}">
                                                        <div class="col-lg-4 px-0">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-12 px-0 page-col text-center p-1 mt-2 select-engineering-event" row-id="${key}" data="${element.id}" unwanted_event_id="${element.unwanted_event}"
                                                                    >
                                                                        <h6 class="mb-0 py-1 text-dark bg-white py-1">
                                                                            Select
                                                                        </h6>
                                                                    </div>
                                                                </div>
                                                                <div class="row" r_id="${key}" e_id="${element.id}" ue_id="${element.unwanted_event}">

                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-lg-8 pe-0 ps-1">
                                                            <div class="container-fluid">
                                                                <div class="row">
                                                                    <div
                                                                        class="col-lg-11 px-0 page-col text-center p-1 mt-2"
                                                                    >
                                                                        <div class="container bg-white">
                                                                            <div class="row">
                                                                                <div class="col-md-12 bg-white pe-0">
                                                                                    <div class="container-fluid">
                                                                                        <div class="row">
                                                                                            <div class="col-10 bg-white">
                                                                                                <input
                                                                                                    class="form-control form-control-sm task-control py-0 px-0 strategy-of-control-details" control-row-id="${key}" data="${element.id}"
                                                                                                    id="event-detail"
                                                                                                />
                                                                                            </div>
                                                                                            <div class="col-2 bg-white pe-0">
                                                                                                <a
                                                                                                    class="bg-white text-decoration-none remove-strategy-of-controls" ref-id="${key}" data="${element.id}"
                                                                                                    href="javascript:;"
                                                                                                >
                                                                                                    <img
                                                                                                        src="{{asset('public')}}/media/icons/cancel.png"
                                                                                                        class="text-xmark"
                                                                                                    />
                                                                                                </a>
                                                                                            </div>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="col-lg-1 pe-0 text-center mt-2"
                                                                    >
                                                                        <div class="btn btn-sm app-btn-selection-outline">
                                                                            <img
                                                                            src="{{asset('public')}}/media/icons/plus-solid.svg"
                                                                            class="btn-xmark plus-engineering-control" unwanted_event_id="${element.unwanted_event}" data="${element.id}"
                                                                            />
                                                                            </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>`;

                                                        let obj = new Object();
                                                        obj['control-key'+key] = {
                                                            'task': current_task.id,
                                                            'hazard': current_hazard.task_hazard,
                                                            'unwanted_event':element.id,
                                                        };
                                                        engineering_controls.push(obj);
                                                        let obj2 = new Object();
                                                        obj2['id'+element.id] = key;
                                                        control_keys.push(obj2);
                                                    }

                                                html += `</div>
                                            </div>
                                        </div>
                            `;

                        });
                        $("#unwanted_events").html(html);
                    }
                }).fail((response) => {
                    Swal.fire({
                    icon: 'error',
                    text: 'Unable to get hazard unwanted events.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
                });
           };

           //administrative controls
           let administrative_controls_details = (control, key, control_type,type, hazard, task, details) => {
            let organization_language = [
            {
                '9' : `@lang('app.organization.human_support')`
            },
            {
                '10': `@lang('app.organization.worker_rotation')`
            },
            {
                '11': `@lang('app.organization.work_breaks')`
            },
            {
                '12': `@lang('app.organization.access_control')`
            },
            {
                '13': `@lang('app.organization.cleaning')`
            },
            {
                '14': `@lang('app.organization.monitering')`
            },
            {
                '15': `@lang('app.organization.workplace_layout')`
            },
            {
                '16': `@lang('app.organization.hydration')`
            }
          ]
          let obligation_language = [
                {
                    '1': `@lang('app.obligations.point_of_operation')`
                },
                {
                    '2': `@lang('app.obligations.speed_limit')`
                },
                {
                    '3': `@lang('app.obligations.safety_distance')`
                },
                {
                    '4': `@lang('app.obligations.body_part_position')`
                },
                {
                    '5': `@lang('app.obligations.weight_limit')`
                },
                {
                    '6': `@lang('app.obligations_height_limit')`
                },
                {
                    '7': `@lang('app.obligations.step')`
                },
                {
                    '8': `@lang('app.obligations.time_limit')`
                }
            ];
            let html = `
            <div class="col-lg-12 px-0 mt-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-3 px-0 page-col text-center p-1">
                            <h6 class="mb-0 py-1 page-col-text cntrl-label bg-white">`;
                                let translated = '';
                              if(type == 'obligation'){
                                if(control_type == 'custom') {
                                    custom_obligation_controls.forEach(c => {
                                        if(c.id == control) {
                                            translated = c.control;
                                        }
                                    });
                                } else {
                                    obligation_language.forEach(l => {
                                    if(control in l){
                                        translated = l[control];
                                    }
                                })
                                }

                              }
                              if(type == 'organization'){
                                if(control_type == 'custom') {
                                    custom_organization_controls.forEach(c => {
                                        if(c.id == control) {
                                            translated = c.control;
                                        }
                                    });
                                } else {
                                    organization_language.forEach(l => {
                                        if(control in l){
                                            translated = l[control];
                                        }
                                    })
                                }

                              }
                            html+=`${translated}</h6>
                          </div>
                          <div
                            class="col-lg-9 pe-0 page-col p-1 d-flex justify-content-between"
                          >
                            <div class="hstack bg-white ps-2 w-100 me-4">
                              <input
                                class="form-control form-control-sm task-control py-0 `;
                                if(type == 'obligation'){
                                    html+=` obligation-details`;
                                }else{
                                    html+=`organization-details`;
                                }
                                html += `"
                                value="${details}"
                                id="${uniqid(control)}" control="${control}" field="${key}" name="administrative-control" task-i="${task}" hazard-i="${hazard}"  type="${type}"
                              />
                            </div>
                            <a
                              class="bg-white text-decoration-none pe-2 delete-administrative-control"
                              href="javascript:;" control="${control}" control-key="${key}" db-type="${control_type}" control-type="${type}" task-d="${task}" hazard-d="${hazard}"
                            >
                              <img
                                src="{{asset('public')}}/media/icons/cancel.png"
                                class="text-xmark"
                              />
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
            `;
            $("#administrative-controls-details").append(html);
           };
            //unique id
            let uniqid = (index) => {
            return Math.round(new Date().getTime() + (Math.random() * 100)) + '' + index;
          };
           /**
            * load data after declaring functions
           */
          //initialize administrative controls
          hazard_instructions(current_task.operation_reference, current_task.id, current_hazard.task_hazard);
    </script>
    @php
      $signature_ids = json_encode($signature_ids);
    @endphp
    <script>
        let signatures = JSON.parse(jsonEscape(`<?php echo $signature_ids; ?>`));

        $(".share-risk-assessment").on('click', function () {
          let username = $("#username").val();
          let mail  = $("#mail").val();
          if(validateEmail(username + '@' + mail + '.com')){
            //console.log(username+'@'+mail+'.com')
            $.ajax({
              type: 'POST',
              url: '{{url("/hazard-manager/share-risk-assessemnt")}}',
              data: {
                '_token': '{{csrf_token()}}',
                'email': username+'@'+mail+'.com',
                'reference': '{{$operation->reference}}',
              },
            }).done(function(response){

              Swal.fire({
                    text: 'Risk Assessment Shared to ' + username+'@'+mail+'.com',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
            }).fail(function (response) {
              //console.log(response)
              Swal.fire({
                    text: 'Enable to share risk assessment.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
             })
          }else{
            Swal.fire({
                    text: 'Enter valid email address.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
          }
         });
         $("#position").change(function () {
          save_general_information();
          });
         $("#work_location").change(function(){
          save_general_information();
         });
         $("#execution_staff").change(function () {
          save_general_information();
          });
         $("#exposed_people").change(function () {
          save_general_information();
          });
          $(document).on('change', '.signature-responsible', function () {  });
          $(document).on('change', '.signature-position', function () {  });
        $("#add-signature").on('click', function(){
            var today = new Date();
var dd = String(today.getDate()).padStart(2, '0');
var mm = String(today.getMonth() + 1).padStart(2, '0'); //January is 0!
var yyyy = today.getFullYear();

today = dd + '/' + mm + '/' + yyyy;
let key = uniqkey(Math.random());
            let html = `
            <div class="row">
                    <div class="col-lg-5 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-3 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                    <input
                                    class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0"
                                    value="@lang('app.manager.update')"
                                  />
                                  <input
                                  type="hidden"
                                    class="form-control form-control-sm signature-control py-2 fs-13 text-center px-0" signature-action="${key}"
                                    value="Update"
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-9 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0 signature-responsible" signature-responsible="${key}"
                                    value=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-7 px-0">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-5 px-0">
                            <div class="container-fluid">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0 signature-position" signature-position="${key}"
                                    value=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-2 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control fs-13 py-2 text-center px-0" signature-date="${key}"
                                    value="${today}"
                                    readonly
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-lg-5 px-0">
                            <div class="container-fluid">
                              <div class="row ">
                                <div class="col-lg-12 text-center bg-white p-1 hstack">
                                  <select class="form-select form-select-sm w-100 ppe-select fs-13 text-center py-0 signature-reason" signature-reason="${key}" style="    background-size: 24px 24px;padding-bottom: 7px !important;
    padding-top: 6px !important;">
                                    <option value="" selected="" disabled>@lang('app.manager.signatures.select')</option>
                                    <option value="Validation">@lang('app.manager.signatures.validation')</option>
                                    <option value="Feedback from Events">@lang('app.manager.signatures.feedback')</option>
                                    <option value="Annual Review">@lang('app.manager.signatures.annual_review')</option>

                                    <option value="Management of change ">@lang('app.manager.signatures.management')</option>
                                    <option value="Safety Alert">@lang('app.manager.signatures.safety_alert')</option>

                                    @if ($system_language != 'en')
                                    <option value="Lessons learned">@lang('app.manager.signatures.lessions')</option>
                                    @endif
                                  </select>
                                  <a class="bg-white text-decoration-none pe-2 delete-signature pb-0 " style="padding-bottom: 2px !important;" signature-key="${key}" href="javascript:;">
                                    <span>x</span>
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            `;
            $("#signatures").append(html);
            signatures.push(key);
        });
        $(document).on('change', '.signature-reason', function(){
            let key = $(this).attr('signature-reason');
            let reason = $(this).val();
            if(reason == 'Validation'){
                $(`[signature-action='${key}']`).val('Validation');
            }else{
                $(`[signature-action='${key}']`).val('Update');
            }
            save_general_information();
        });
        $(document).on('click', '.delete-signature', function(){
          let key = $(this).attr('signature-key');
          $(this).parent().parent().parent().parent().parent().parent().parent().parent().remove();
          signatures.splice($.inArray(key, signatures), 1);
          save_general_information();
        });
        $(document).on('click', ".save-general-information", function(){
          save_general_information();
          setTimeout(function(){
        window.location.href = '{{url("/home")}}'
});
        });
        /**
         *
         * methods
        */
       const save_general_information = () => {
        const position = $("#position").val();
            const work_location = $("#work_location").val();
            const execution_staff = $("#execution_staff").val();
            const exposed_people = $("#exposed_people").val();
            let operation_signatures = [];
            signatures.forEach(key => {
                let obj = new Object();
                obj['action'] = $(`[signature-action='${key}']`).val();
                obj['responsible'] = $(`[signature-responsible='${key}']`).val();
                obj['position'] = $(`[signature-position='${key}']`).val();
                obj['date'] = $(`[signature-date='${key}']`).val();
                obj['reason'] = $(`[signature-reason='${key}']`).val();
                operation_signatures.push(obj)
            });
            //console.log(operation_signatures);
            $.ajax({
                type: 'POST',
                url: '{{url("/hazard-manager/general-information/save")}}',
                data: {
                    '_token': '{{csrf_token()}}',
                    'position': position,
                    'work_location': work_location,
                    'execution_staff': execution_staff,
                    'exposed_people': exposed_people,
                    'operation_signatures': operation_signatures,
                    'operation_reference': '{{$operation->reference}}'
                },
            }).done(function(response){
              return;
            }).fail(function (response) {
                //console.log(response)
                Swal.fire({
                    text: 'Unable to save general information.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
             });
       }
        const validateEmail = (email) => {
  return String(email)
    .toLowerCase()
    .match(
      /^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
    );
};
        let uniqkey = (index) => {
            return Math.round(new Date().getTime() + (Math.random() * 100)) + '' + index;
          };
    </script>
    @php
        $signage_ids = json_encode($signage_ids);
        $monitering_ids = json_encode($monitering_ids);
        $equipment_ids = json_encode($equipment_ids);
        $testing_ids = json_encode($testing_ids);
    @endphp
    <script>
        let signage = JSON.parse(`<?php echo $signage_ids; ?>`),
        monitering = JSON.parse(`<?php echo $monitering_ids; ?>`),
        equipments = JSON.parse(`<?php echo $equipment_ids; ?>`),
        testing = JSON.parse(`<?php echo $testing_ids; ?>`);
        $(document).on('change', 'input[name=people]', function () {
            save_conformity();
         });
         $(document).on('change', 'input[name=workplace]', function () {
          save_conformity();
          });
          $(document).on('change', 'input[name=chemicals]', function(){
            save_conformity();
          })
          $(document).on('change', 'input[name=equipment]', function () {
            save_conformity();
           })
$(document).on('change', '.signage-select', function () {
  const status = $(this).attr('signage-plus');
  const ref = $(this).attr('reference');
  if($(this).val() != ''){
    $(this).addClass('selected-list')
    $(this).addClass('border-app-selected-list')
    $(this).parent().parent().addClass('selected-list')
    save_conformity();
    if(status != 1){
      plus_signage();
      $(this).attr('signage-plus', 1);
    }
    $(`.signage-details[reference='${ref}']`).parent().parent().parent().parent().parent().parent().parent().parent().removeClass('d-none');
  }else{
    $(this).removeClass('selected-list')
    $(this).removeClass('border-app-selected-list')
    $(this).parent().parent().removeClass('selected-list')
    $(`.signage-details[reference='${ref}']`).parent().parent().parent().parent().parent().parent().parent().parent().addClass('d-none');
  }
 })
 $(document).on('change', '.monitering-select', function(){
  const status = $(this).attr('monitering-plus');
  const ref = $(this).attr('reference');
  if($(this).val() != ''){
    $(this).addClass('selected-list')
    $(this).addClass('border-app-selected-list')
    $(this).parent().parent().addClass('selected-list')
    save_conformity();
    if(status != 1){
      plus_monitering();
      $(this).attr('monitering-plus');
    }
    $(`.monitering-details[reference='${ref}']`).parent().parent().parent().parent().parent().parent().parent().parent().removeClass('d-none');
  }else{
    $(this).removeClass('selected-list')
    $(this).removeClass('border-app-selected-list')
    $(this).parent().parent().removeClass('selected-list')
    $(`.monitering-details[reference='${ref}']`).parent().parent().parent().parent().parent().parent().parent().parent().addClass('d-none');
  }
 })
 $(document).on('change', '.testing-select', function () {
  let package_reference = $(this).attr('reference-package');
  const status = $(this).attr('testing-plus');
  const ref = $(this).attr('reference');
  if($(this).val() != ''){
    $(this).addClass('selected-list')
    $(this).addClass('border-app-selected-list')
    $(this).parent().parent().addClass('selected-list')
    save_conformity();
    if(status != 1){
      plus_testing(package_reference);
      $(this).attr('testing-plus', 1);
    }
    $(`.testing-details[reference='${ref}']`).parent().parent().parent().parent().parent().removeClass('d-none');
  }else{
    $(this).removeClass('selected-list')
    $(this).removeClass('border-app-selected-list')
    $(this).parent().parent().removeClass('selected-list')
    $(`.testing-details[reference='${ref}']`).parent().parent().parent().parent().parent().addClass('d-none');
  }
  })
  $(document).on('change', '.people-details', function () {
    save_conformity();
   })
   $(document).on('change', '.workplace-details', function(){
    save_conformity();
   })
   $(document).on('change', '.chemical-details', function () {
    save_conformity();
    })
    $(document).on('change', '.equipment-details', function(){
      save_conformity();
    })
    $(document).on('change', '.testing-details', function () {
      save_conformity();
     })
     $(document).on('change', '.monitering-details', function () {
      save_conformity();
      });
      $(document).on('change', '.signage-details', function () {
        save_conformity();
       })
        $(document).on('click', '.delete-signage', function(){
            let reference = $(this).attr('reference');
            signage.splice($.inArray(reference, signage), 1);
            $(this).parent().parent().parent().parent().parent().parent().parent().parent().remove();
            save_conformity();
        });
         $(document).on('click', '.delete-monitering', function(){
            let reference = $(this).attr('reference');
            signage.splice($.inArray(reference, monitering), 1);
            $(this).parent().parent().parent().parent().parent().parent().parent().parent().remove();
            save_conformity();
         });
         $(document).on('click', '.plus-equipment', function(){
            let key1 = uniqkey(Math.random() + '' + Math.random());
            let key2 = uniqkey(Math.random());
            let sr = count_equipment_sr();
            let html = `
            <div class="row mt-2" reference-package="${key1}">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div
                            class="col-lg-12 px-0 d-flex justify-content-between page-col-gray p-1"
                          >
                          <input type="text" value="" placeholder="Add a Name" reference-package="${key1}"  class=" package-name mb-0 py-1 text-dark bg-white fw-bold ps-3 fs-13 form-control form-control-sm border-0">
                            <a
                              class="text-decoration-none pe-2 delete-equipment" reference-package="${key1}"
                              href="javascript:;"
                            >
                              <img src="{{asset('public')}}/media/icons/cancel.png" class="text-xmark" />
                            </a>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 text-center p-1"></div>
                    <div class="col-lg-12 px-0 p-1">
                        <div class="container-fluid">
                            <div class="row mt-2">
                                <div class="col-lg-4 ps-0 pe-1 text-center">
                                  <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                    <input
                                      class="btn-check"
                                      type="checkbox"
                                      value="Certification" name="equipment" reference-package="${key1}"
                                      id="certification${key1}"
                                    />
                                    <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="certification${key1}">
                                      @lang('app.manager.checks.certification')
                                    </label>
                                  </div>
                                </div>
                                <div class="col-lg-8 px-0 text-center page-col p-1">
                                  <div class="container-fluid">
                                    <div class="row bg-white">
                                      <div class="col-lg-12 bg-white">
                                        <input
                                          class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 equipment-details" reference="Certification" reference-package="${key1}"
                                          value=""
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="row mt-2">
                                <div class="col-lg-4 ps-0 pe-1 text-center">
                                  <div class="form-check form-check-inline justify-content-center d-flex mb-0 ps-0 me-0 pe-0">
                                    <input
                                      class="btn-check"
                                      type="checkbox" reference-package="${key1}"
                                      value="Calibration" name="equipment"
                                      id="calibration${key1}"
                                    />
                                    <label class="btn btn-outline-primary w-100 fs-13 check-rounded text-start conformity-check-ps" for="calibration${key1}">
                                      @lang('app.manager.checks.calibration')
                                    </label>
                                  </div>
                                </div>
                                <div class="col-lg-8 px-0 text-center page-col p-1">
                                  <div class="container-fluid">
                                    <div class="row bg-white">
                                      <div class="col-lg-12 bg-white">
                                        <input
                                          class="form-control form-control-sm mt-1 signature-control border-pre-job-control py-0 text-center px-0 equipment-details" reference="Calibration" reference-package="${key1}"
                                          value=""
                                        />
                                      </div>
                                    </div>
                                  </div>
                                </div>
                              </div>
                              <div class="equipment-testing" reference-package="${key1}">
                                <div class="row mt-2" reference="${key2}" reference-package="${key1}">
                                    <div class="col-lg-4 ps-0 pe-1">
                                      <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                                        <div class="container bg-transparent conformity-select-container">
                                          <input type="hidden" name="equipment" value="Testing" reference="${key2}" reference-package="${key1}">
                                          <select
                                            class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select" reference="${key2}" reference-package="${key1}"
                                          >
                                          <option value="" selected>@lang('app.manager.test.select')</option>
                                          <option value="Integrity test">@lang('app.manager.tests.integrity_test')</option>
                                            <option value="Energy test">@lang('app.manager.tests.energy_test')</option>
                                            <option value="Isolation test">@lang('app.manager.tests.isolation_test')</option>
                                            <option value="Power test">@lang('app.manager.tests.power_test')</option>
                                            <option value="Pressure test">@lang('app.manager.tests.pressure_test')</option>
                                            <option value="Capacity test">@lang('app.manager.tests.capacity_test')</option>
                                            <option value="Load test">@lang('app.manager.tests.load_test')</option>
                                            <option value="Corrosion test">@lang('app.manager.tests.corrosion_test')</option>
                                            <option value="Other">@lang('app.manager.tests.other')</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-lg-8 px-0 text-center d-none">
                                      <div class="container-fluid">
                                        <div class="row">
                                          <div
                                            class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                          >
                                            <div
                                              class="container-fluid px-2 d-flex justify-content-start bg-white"
                                            >
                                              <input
                                                class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details" reference="${key2}" reference-package="${key1}"
                                                value=""
                                              />
                                            </div>
                                            <a
                                              class="bg-white text-decoration-none ps-3 delete-testing-list" reference="1" reference-package="1"
                                              href="javascript:;"
                                            >
                                              <img
                                                src="{{asset('public')}}/media/icons/cancel.png"
                                                class="text-xmark"
                                              />
                                            </a>
                                          </div>
                                          <!--<div
                                            class="col-lg-1 px-0 text-center p-1 mt-1 btn btn-sm app-btn-selection-outline"
                                          >
                                          <a class="text-decoration-none plus-testing-list" reference="1" reference-package="1">
                                            <img
                                              src="{{asset('public')}}/media/icons/plus-solid.svg"
                                              class="btn-xmark"
                                            />
                                        </a>
                                          </div>-->
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                              </div>
                        </div>
                    </div>
                  </div>
            `;
            $("#equipment-tools").append(html);
            equipments.push(key1);
            testing.push(key2);
         });
         $(document).on('click', '.delete-equipment', function () {
            let reference = $(this).attr('reference-package');
            equipments.splice($.inArray(reference, equipments), 1);
            $(".delete-testing-list").each(function(){
                if($(this).attr('reference-package') == reference){
                    testing.splice($.inArray($(this).attr('reference'), testing), 1);
                }
            });
            $(this).parent().parent().parent().parent().parent().remove();
            save_conformity();
         });
         $(document).on('change', '.package-name', function () {
          save_conformity();
         })
          $(document).on('click', '.delete-testing-list', function () {
            let reference = $(this).attr('reference');
            testing.splice($.inArray(reference, testing), 1);
            $(this).parent().parent().parent().parent().parent().remove();
            save_conformity();
          });
          $(document).on('click', '.save-conformity', function(){
            save_conformity();
            setTimeout(function(){
              window.location.href = '{{url("/home")}}'
          });
         });
         /**
          * methods
         */
        const plus_testing = (package) => {
            let key = uniqkey(Math.random());
            let html = `
                                  <div class="row mt-2" reference="${key}" reference-package="${package}">
                                    <div class="col-lg-4 ps-0 pe-1">
                                      <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                                        <div class="container bg-transparent conformity-select-container">
                                          <input type="hidden" name="equipment" value="Testing" reference="${key}" reference-package="${package}">
                                          <select
                                            class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 testing-select" reference="${key}" reference-package="${package}"
                                          >
                                          <option value="" selected>@lang('app.manager.test.select')</option>
                                          <option value="Integrity test">@lang('app.manager.tests.integrity_test')</option>
                                            <option value="Energy test">@lang('app.manager.tests.energy_test')</option>
                                            <option value="Isolation test">@lang('app.manager.tests.isolation_test')</option>
                                            <option value="Power test">@lang('app.manager.tests.power_test')</option>
                                            <option value="Pressure test">@lang('app.manager.tests.pressure_test')</option>
                                            <option value="Capacity test">@lang('app.manager.tests.capacity_test')</option>
                                            <option value="Load test">@lang('app.manager.tests.load_test')</option>
                                            <option value="Corrosion test">@lang('app.manager.tests.corrosion_test')</option>
                                            <option value="Other">@lang('app.manager.tests.other')</option>
                                          </select>
                                        </div>
                                      </div>
                                    </div>
                                    <div class="col-lg-8 px-0 text-center d-none">
                                      <div class="container-fluid">
                                        <div class="row">
                                          <div
                                            class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                          >
                                            <div
                                              class="container-fluid px-2 d-flex justify-content-start bg-white"
                                            >
                                              <input
                                                class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 testing-details" reference="${key}" reference-package="${package}"
                                                value=""
                                              />
                                            </div>
                                            <a
                                              class="bg-white text-decoration-none ps-3 delete-testing-list" reference="${key}" reference-package="${package}"
                                              href="javascript:;"
                                            >
                                              <img
                                                src="{{asset('public')}}/media/icons/cancel.png"
                                                class="text-xmark"
                                              />
                                            </a>
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                  </div>
            `;

            $(`[reference-package='${package}'] .equipment-testing`).append(html);
            testing.push(key);
        }
        const plus_signage = () => {
          let key = uniqkey(Math.random());
            let html = `
                  <div class="row mt-2" reference="${key}">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                            <div class="container bg-transparent conformity-select-container">
                              <select
                              class="form-select form-select-sm conformity-select pt-0 pb-0 fs-13 signage-select" reference="${key}"
                            >
                              <option value="" selected>@lang('app.manager.checks.signage')</option>
                              <option value="Safety Sign">@lang('app.manager.signage.safety_sign')</option>
                              <option value="Color Code labeling">@lang('app.manager.signage.color_code_labeling')</option>
                              <option value="Floor Marking">@lang('app.manager.signage.floor_marking')</option>
                            </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 d-none">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                >
                                  <div
                                    class="container-fluid d-flex justify-content-start px-2 bg-white"

                                  >
                                    <input
                                      class="form-control form-control-sm signature-control border-pre-job-control py-0 mt-1 text-center px-0 signage-details" reference="${key}"
                                      value=""
                                    />
                                  </div>
                                  <a
                                    class="bg-white text-decoration-none ps-3 delete-signage" reference="${key}"
                                    href="javascript:;"
                                  >
                                    <img
                                      src="{{asset('public')}}/media/icons/cancel.png"
                                      class="text-xmark"
                                    />
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            `;
            $("#sigange-rows").append(html);
            signage.push(key);
        }
        const plus_monitering = () => {
          let key = uniqkey(Math.random());
            let html = `
                  <div class="row mt-2" reference="${key}">
                    <div class="col-lg-4 ps-0 pe-1">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 page-col p-1 check-rounded">
                            <div class="container bg-transparent conformity-select-container">
                              <select
                              class="form-select form-select-sm conformity-select pt-0 fs-13 pb-0 monitering-select" reference="${key}"
                            >
                              <option value="" selected>
                                @lang('app.manager.checks.monitering')
                              </option>
                              <option value="EX Atmosphere monitering">@lang('app.manager.monitering.ex_atm')</option>
                              <option value="Noise Level monitering">@lang('app.manager.monitering.noise_level')</option>
                              <option value="Oxygen Level monitering">@lang('app.manager.monitering.oxygen')</option>
                              <option value="Airflow monitering">@lang('app.manager.monitering.airflow')</option>
                              <option value="OEL (Occupational Exposer Level)">@lang('app.manager.monitering.oel')</option>
                              <option value="Heat Monitering">@lang('app.manager.monitering.heat')</option>
                              <option value="Humidity Monitering">@lang('app.manager.monitering.humidity')</option>
                              <option value="Other">@lang('app.manager.monitering.other')</option>
                            </select>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-8 px-0 d-none">
                      <div class="container-fluid">
                        <div class="row">
                          <div class="col-lg-12 px-0 text-center">
                            <div class="container-fluid">
                              <div class="row">
                                <div
                                  class="col-lg-12 page-col p-1 bg-white d-flex justify-content-between"
                                >
                                  <div
                                    class="container-fluid d-flex justify-content-start px-2 bg-white"
                                  >
                                    <input
                                      class="form-control form-control-sm signature-control mt-1 border-pre-job-control py-0 text-center px-0 monitering-details" reference="${key}"
                                      value=""
                                    />
                                  </div>
                                  <a
                                    class="bg-white text-decoration-none ps-3 delete-monitering" reference="${key}"
                                    href="javascript:;"
                                  >
                                    <img
                                      src="{{asset('public')}}/media/icons/cancel.png"
                                      class="text-xmark"
                                    />
                                  </a>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
            `;
            $("#monitering-rows").append(html);
            monitering.push(key);
        }
        const save_conformity = () => {
            const checked_people = [];
            const checked_workplace = [];
            const checked_signage = [];
            const checked_monitering = [];
            const checked_chemicals = [];
            const checked_equipments = [];
            $("input[name=people]").each(function () {
                let obj = new Object();
                const checked = $(this).val()
                obj['checked'] = checked;
                if($(this).is(':checked')){
                    obj['state'] = 1;
                }else{
                    obj['state'] = 0;
                }
                $(".people-details").each(function () {
                    if(checked == $(this).attr('reference')){
                        obj['details'] = $(this).val();
                    }
                 })
                checked_people.push(obj);
            });
            $("input[name=workplace]").each(function () {
                let obj = new Object();
                const checked = $(this).val()
                obj['checked'] = checked;
                if($(this).is(':checked')){
                    obj['state'] = 1;
                }else{
                    obj['state'] = 0;
                }
                $(".workplace-details").each(function () {
                    if(checked == $(this).attr('reference')){
                        obj['details'] = $(this).val();
                    }
                 })
                checked_workplace.push(obj);
            });
            signage.forEach(key => {
              let obj = new Object();
                    let checked = 0;
                    $(".signage-select").each(function () {
                        if($(this).attr('reference') == key){
                          if($(this).val() != ''){
                            obj['select'] = $(this).val();
                            obj['checked'] = $(this).val();
                            checked = 1;
                          }
                        }
                     });
                    $(".signage-details").each(function () {
                        if($(this).attr('reference') == key){
                            if(checked == 1){
                              obj['details'] = $(this).val();
                            }
                        }
                     });
                    checked_signage.push(obj);
            });
            monitering.forEach(key => {
              let obj = new Object();
              let checked = 0;

                    $(".monitering-select").each(function () {
                        if($(this).attr('reference') == key){
                            if($(this).val() != ''){
                              obj['select'] = $(this).val();
                            obj['checked'] = $(this).val();
                            checked = 1;
                            }
                        }
                     });
                    $(".monitering-details").each(function () {
                        if($(this).attr('reference') == key){
                            if(checked == 1){
                              obj['details'] = $(this).val();
                            }
                        }
                     });
                    checked_monitering.push(obj);
            });
            $("input[name=chemicals]").each(function () {
                let obj = new Object();
                const checked = $(this).val()
                obj['checked'] = checked;
                if($(this).is(':checked')){
                    obj['state'] = 1;
                }else{
                    obj['state'] = 0;
                }
                $(".chemical-details").each(function () {
                    if(checked == $(this).attr('reference')){
                        obj['details'] = $(this).val();
                    }
                 })
                checked_chemicals.push(obj);
            });
            equipments.forEach(package => {
                let obj = new Object();
                obj['equipment'] = package;
                $(".package-name").each(function () {
                    //console.log($(this).attr('reference-package'));
                  if($(this).attr('reference-package') == package){
                    //console.log($(this).val());
                    obj['package-name'] = $(this).val();
                  }
                 })
                let certification = [];
                let calibration = [];
                let testings = [];
                $("input[name=equipment]").each(function(){
                    let reference = $(this).val();
                    if($(this).attr('reference-package') == package){
                        if($(this).val() == 'Certification'){
                            let ob = new Object();
                            ob['checked'] = reference;
                            if($(this).is(':checked')){
                                ob['state'] = 1;
                            }else{
                                ob['state'] = 0;
                            }
                            $(".equipment-details").each(function () {
                                if(reference == $(this).attr('reference') && package == $(this).attr('reference-package')){
                                    ob['details'] = $(this).val();
                                }
                            })
                            certification.push(ob);
                        }
                        if($(this).val() == 'Calibration'){
                            let ob = new Object();
                            ob['checked'] = reference;
                            if($(this).is(':checked')){
                                ob['state'] = 1;
                            }else{
                                ob['state'] = 0;
                            }
                            $(".equipment-details").each(function () {
                                if(reference == $(this).attr('reference') && package == $(this).attr('reference-package')){
                                    ob['details'] = $(this).val();
                                }
                            })
                            calibration.push(ob)
                        }
                        if($(this).val() == 'Testing'){
                            let key = $(this).attr('reference');
                            let ob = new Object();
                            ob['checked'] = reference;
                            $(".testing-select").each(function () {
                                if($(this).attr('reference') == key){
                                    ob['select'] = $(this).val();
                                }
                            });
                            $(".testing-details").each(function () {
                                if($(this).attr('reference') == key){
                                    ob['details'] = $(this).val();
                                }
                            });
                            testings.push(ob);
                        }
                    }
                });
                obj['certification'] = certification;
                obj['calibration'] = calibration;
                obj['testings'] = testings;
                checked_equipments.push(obj);
            });
            //console.log(checked_people)
            //console.log(checked_workplace)
            //console.log(checked_signage)
            //console.log(checked_monitering)
            //console.log(checked_chemicals)
            //console.log(checked_equipments)
            $.ajax({
                type: 'POST',
                url: '{{url("/hazard-manager/conformity/save")}}',
                data: {
                    '_token': '{{csrf_token()}}',
                    'operation_reference': '{{$operation->reference}}',
                    'people': checked_people,
                    'workplace': checked_workplace,
                    'signage': checked_signage,
                    'monitering': checked_monitering,
                    'chemicals': checked_chemicals,
                    'equipments': checked_equipments,
                },
            }).done((response)=>{
              return;
            }).fail((response)=>{
                //console.log(response);
                Swal.fire({
                    icon: 'error',
                    text: 'Unable to save conformity.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
            });
        }
        let count_equipment_sr = () => {
            return equipments.length + 1;
        };
    </script>
    @php
        $protections = json_encode($protections);
        $company_ids = json_encode($company_ids);
    @endphp
    <script>
        let protections = JSON.parse(jsonEscape(`<?php echo $protections; ?>`));
        let company_keys = JSON.parse(`<?php echo $company_ids; ?>`);
        $(".date-of-next-purchase").on('change', function () {
          save_ppe();
         });
         $(document).on('click', ".ppe-remove", function () {
            const key = $(this).attr('operation-ppe-key');
            if(company_keys.length > 0){
                        company_keys.forEach((k, i) => {
                            if(k == key){
                                company_keys.splice(i,1);
                            }
                        });
            }
            $(this).parent().parent().remove();
            save_ppe();
        })
        $(".plus-operation-ppe").on('click', function () {
            let key = uniqid(Math.random());
            let html = `
            <div class="row">
                <div class="col-1 text-end" style="width: 4.33333333% !important;">
                            <span class="ppe-remove" operation-ppe-key="${key}">x</span>
                        </div>
            <div class="col-11 ps-0" style="width: 95.666667% !important;">
            <div class="container-fluid" operation="${key}">
                        <div class="row">
                          <div class="col px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <select
                                    class="form-select form-select-sm ppe-select text-center py-0 protection" protection="${key}"
                                  >
                                    <option value="" selected="">List</option>`;
                                  protections.forEach(element => {
                                    html+= `<option value="${element.id}">${element.name}</option>`
                                  });
                                    html+=`</select>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-3 px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <select
                                    class="form-select form-select-sm ppe-select text-center py-0 type" type="${key}"
                                  >
                                    <option value="" selected="">List</option>
                                  </select>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-3 px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control py-2 text-center px-0 specifications" specifications="${key}"
                                    placeholder=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control py-2 text-center px-0 brand" brand="${key}"
                                    placeholder=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                    class="form-control form-control-sm signature-control py-2 text-center px-0 reference" ref="${key}"
                                    placeholder=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col px-0">
                            <div class="container-fluid bg-white">
                              <div class="row">
                                <div class="col-lg-12 text-center bg-white p-1">
                                  <input
                                  type="number"
                                    class="form-control form-control-sm signature-control py-2 text-center px-0 quantity" quantity="${key}"
                                    placeholder=""
                                  />
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>

                    </div>
                </div>
            `;
            $("#operation-ppe").append(html);

            html = `
            <div class="row " company="${key}">
                                <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-type="${key}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-brand="${key}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-reference="${key}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-quantity="${key}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
            `;
            $("#company-ppe").append(html);
            company_keys.push(key);
        });
        $(document).on('change', '.protection', function(){
            let key = $(this).attr('protection');
            let protection = $(this).val();
            $.ajax({
                type: 'GET',
                url: '{{url("/hazard-manager/ppe/types")}}',
                data: {
                    'protection': protection
                },
            }).done(function (response) {
                $(`[type='${key}']`).empty().trigger('change');
                $(`[type='${key}']`).append(new Option("List", "", true, false)).trigger('change');
                if(response.length > 0){
                response.forEach(element => {
                    $(`[type='${key}']`).append(new Option(element.name, element.id + '|' + element.name, false, false)).trigger('change');
                });
                }
                save_ppe();
             }).fail(function (response) {
                //console.log(response)
                Swal.fire({
                    icon: 'error',
                    text: 'Unable to save conformity.',
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
             })
        });
        $(document).on('change', '.type', function () {
            let key = $(this).attr('type');
            //console.log($(this).val());
            if($(this).val() != '' && $(this).val() != null && $(this).val() != undefined){
                let type = $(this).val().split('|');
            if(type.length > 0){
                $(`[company-type='${key}']`).val(type[1]);
                $(`[company-type='${key}']`).attr('data', type[0]);
            }
            save_ppe();
            }
         });
        $(document).on('change', '.specifications', function() {
            let key = $(this).attr('specifications');
            $(`[company-specifications='${key}']`).val($(this).val());
            save_ppe();
        });
        $(document).on('change', '.brand', function () {
            let key = $(this).attr('brand');
            $(`[company-brand='${key}']`).val($(this).val());
            save_ppe();
         });
        $(document).on('change', '.reference', function () {
            let key = $(this).attr('ref');
            $(`[company-reference='${key}']`).val($(this).val());
            save_ppe();
         });
        $(document).on('change', '.quantity', function () {
            let key = $(this).attr('quantity');
            $(`[company-quantity='${key}']`).val($(this).val());
            save_ppe();
         });
         $(document).on('click', '.save-ppe', function(){
            const operation_ppe = [];
            //console.log(company_keys);
            company_keys.forEach(key => {
                let protection = $(`[protection='${key}']`).val(),
                type = $(`[type='${key}']`).val().split('|'),
                specifications = $(`[specifications='${key}']`).val(),
                brand = $(`[brand='${key}']`).val(),
                reference = $(`[ref='${key}']`).val(),
                quantity = $(`[quantity='${key}']`).val();
                if(protection != '' && protection != null && protection != undefined){
                    let obj = new Object();
                    obj['protection'] = protection;
                    obj['type'] = type[0];
                    obj['specifications'] = specifications;
                    obj['brand'] = brand;
                    obj['reference'] = reference;
                    obj['quantity'] = quantity;
                    operation_ppe.push(obj);
                }
            });
            //console.log($(".date-of-next-purchase").val());
            save_ppe();
                setTimeout(function(){
                  window.location.href = '{{url("/home")}}'
                });
         });
         /**
          *
          * methods
         */
        const save_ppe = () => {
          const operation_ppe = [];
            company_keys.forEach(key => {
                let protection = $(`[protection='${key}']`).val(),
                type = $(`[type='${key}']`).val().split('|'),
                specifications = $(`[specifications='${key}']`).val(),
                brand = $(`[brand='${key}']`).val(),
                reference = $(`[ref='${key}']`).val(),
                quantity = $(`[quantity='${key}']`).val();
                if(protection != '' && protection != null && protection != undefined){
                    let obj = new Object();
                    obj['protection'] = protection;
                    obj['type'] = type[0];
                    obj['specifications'] = specifications;
                    obj['brand'] = brand;
                    obj['reference'] = reference;
                    obj['quantity'] = quantity;
                    operation_ppe.push(obj);
                }
            });
            $.ajax({
                    type: 'POST',
                    url: '{{url("/hazard-manager/ppe/save")}}',
                    data: {
                        '_token': '{{csrf_token()}}',
                        'operation_reference': '{{$operation->reference}}',
                        'operation_ppe': operation_ppe,
                        'date_of_next_purchase': $(".date-of-next-purchase").val(),
                    },
                }).done(function (response) {
             return;
                 }).fail(function (response) {
                    //console.log(response);
                    Swal.fire({
                    icon: 'error',
                    text: "enable to save ppe.",
                    confirmButtonText:'Ok',
                    customClass: {
                        confirmButton: "btn app-btn-primary pt-1 pb-1 px-5"
                    }
                  });
                 });
        }
        Array.prototype.unique = function() {
          var a = this.concat();
          for(var i=0; i<a.length; ++i) {
            for(var j=i+1; j<a.length; ++j) {
              if(a[i] === a[j])
                a.splice(j--, 1);
            }
          }
          return a;
        };
        const company_ppe = () => {
          $.ajax({
            type: 'GET',
            url: '{{url("/hazard-manager/ppe/company-ppe/calculate")}}/'+'<?php echo $operation->reference; ?>',
          }).done(function (response) {
            if(response.length > 0){
              let html = ``;
              response.forEach(element => {
                html += `
            <div class="row " company="">
                                <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-type="" value="${element.type_name}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-brand="" value="${element.brand}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-reference="" value="${element.reference}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                                  <div class="col px-0">
                                    <div class="container-fluid bg-white">
                                      <div class="row">
                                        <div class="col-lg-12 text-center bg-white p-1">
                                          <input
                                            class="form-control form-control-sm signature-control py-2 text-center px-0" company-quantity="" value="${element.grouped_qty}" readonly
                                          />
                                        </div>
                                      </div>
                                    </div>
                                  </div>
                            </div>
            `;
              })
            $("#company-ppe").html(html);
            }
           });
        };
    </script>
    @if(Session::get('success'))
    <script>
          $(document).ready(function () {
         Swal.fire({
            text: '{{Session::get("success")}}',
            customClass: {
              confirmButton: 'btn btn-outline-green pt-1 pb-1 px-5'
            },
            buttonsStyling: false
          });


    })

    </script>
    @endif
    @if(Session::get('error'))
    <script>
         Swal.fire({
            text: '{{Session::get("error")}}',
            customClass: {
              confirmButton: 'btn btn-outline-red pt-1 pb-1 px-5'
            },
            buttonsStyling: false
          });
    </script>
    @endif
  </body>
</html>
