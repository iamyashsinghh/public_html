<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>mail Template</title>
</head>
<style>
    /* font style */
    @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap');

    * {
        margin: 0px;
        padding: 0px;
        box-sizing: border-box;
        font-family: 'Poppins';
    }

    td {
        display: block;
    }

    h3 {
        font-size: 1rem;
        text-align: center;
    }
</style>

<body>
    <div class="main-container" style="max-width: 45rem; margin: auto;">
        <table class="form-container" style="max-width: 100%; border: 1px solid gray; margin: 0 auto; padding: 2rem 0rem;">
            <tr>
                <td>
                    <table style="width: 100%;">
                        <tr>
                            <td>
                                <h3 class="p-500" style="font-size: 1rem; text-align: center; margin-bottom: 1rem;">
                                    Respond within 24 hours for the best chance of booking the services.</h3>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <img src="{{asset('images/lead_notify_hero_banner.png')}}" alt="" class="logo" style="width: 40%; margin:auto;  margin: 1rem auto; display: block;">
                            </td>
                        </tr>
                        <tr>
                            <td class="lead-header" style="max-width: 100%; padding: 5%; background: #FFEEEE; display: block; margin-bottom: 1rem; text-align: center;">
                                <h3 style="font-weight: 600; margin-bottom: .5rem;">You've received a new lead
                                    interested in booking
                                    with Wedding Banquets.</h3>
                                <h3 style="font-weight: 500;  margin-bottom: .5rem;">{{$data['lead_name']}} is interested in learning
                                    more about the
                                    Wedding Services you offer.</h3>
                                <h3 style="font-weight: 600; margin-bottom: .5rem;">Message from {{$data['lead_name']}}: {{date('d-M-Y h:i a')}}</h3>
                            </td>
                        </tr>
                        <tr>
                            <td class="lead-details " style="border: 1px solid black;  max-width:30rem; padding: 1rem 3rem;  margin: 1rem auto;">
                                <table style="width: 100%">
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">Event Name :</h3>
                                            <p style="display: inline; float: right;">{{$data['event_name']}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">Event Date :</h3>
                                            <p style="display: inline; float: right">{{$data['event_date']}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">Event Slot :</h3>
                                            <p style="display: inline; float: right">{{$data['event_slot']}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">Email :</h3>
                                            <p style="display: inline; float: right">{{$data['lead_email']}}</p>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <h3 style="display: inline;">Phone :</h3>
                                            <p style="display: inline; float: right">{{$data['lead_mobile']}}</p>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                    </table>
                </td>
            </tr>
            <tr>
                <td style="text-align: center; margin-bottom: 1rem;">
                    <h3 class="p-600" style="font-weight: 600; margin-bottom: 3%;">Respond to this lead from your
                        Wedding Banquets
                        account.</h3>
                    <a href="https://wbcrm.in/vendor" class="response-btn" style="background-color: #870808; text-transform: uppercase; border: none; color: white; margin-bottom: .5rem; padding: .5rem 1rem; border-radius: .5rem; cursor: pointer;">Respond</a>
                </td>
            </tr>
            <tr>
                <td class="lead-footer" style="padding: 0rem 2rem; text-align: center;">
                    <h4 class="p-400" style="font-weight: 400;">We are sending you this e-mail because you
                        signed up as a vendor in Wedding Banquets. If you want to make any changes, log in to
                        your admin page. Wedding Banquets strictly comply with LSSICE and GDPR. We encourage you
                        to review our Terms of Use and Privacy Policy. © 2022.
                    </h4>
                </td>
            </tr>
        </table>
    </div>
</body>

</html>