<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Agreement</title>
    <style>
        p{
            padding: 12px;
            line-height: 25px;
        }
        span{
            border-bottom: 1px solid black;
        }
    </style>
</head>
<body>
    @if (!empty($user) && (!empty($rentItem)))
        <p>
            <b>Name</b> <span>{{$user->full_name}}</span> <b>Phone</b> <span>{{$user->mobile}}</span> <b>Address</b> <span>{{!empty($rentItem->ads) ? $rentItem->ads->address : ''}}</span> 
            <b>Start Date</b> <span>{{!empty($rentItem->start) ? \Carbon\Carbon::parse($rentItem->start)->format('d-m-Y') : ''}}</span>
            <b>End Date</b> <span>{{!empty($rentItem->end) ? \Carbon\Carbon::parse($rentItem->end)->format('d-m-Y') : ''}}</span> 
            <b>Start Time</b> <span>{{!empty($rentItem->end) ? \Carbon\Carbon::parse($rentItem->end)->format('H:i') : ''}}</span> <b>End Time</b> <span>{{!empty($rentItem->end) ? \Carbon\Carbon::parse($rentItem->end)->format('H:i') : ''}}</span>
        </p>
        <p>♫ Reservations are taken starting February 1 of the current year for residents and their immediate family living within the New Bremen School District (and must be used by the resident or immediate family). Non-residents of the school district may reserve facilities starting March 1 of the current year. In the case of a wedding and/or wedding reception at the Crown Pavilion, the reservation must be made by the bride or groom or their parents of which at least one lives in the New Bremen School District.
            A non-refundable fee of $50.00 is required to reserve a shelter house at the Bremenfest Park or the Jaycee Park which includes one full day from dawn until closing. The Crown Pavilion is a non-refundable fee of $100.00 for each day of use and each day of setup and teardown, plus the $150.00 security deposit. See Crown Pavilion Attachment "A"
            The kitchen of the East shelter house can be rented for a $25.00 non-refundable fee.
            The shelter houses are available after 11:00 a.m. and to 10:00 p.m. Monday-Thursday
            Hours are 11:00 am to 12:00 midnight Friday, Saturday and Sunday. The Crown Pavilion closes at 12:00 midnight with music ending at 11:00 p.m.
        </p>
        <p>
            ✓ All trash must be picked up, all lights turned off, and all doors locked before renter leaves.
            ✓ No glass is allowed.
        </p>
        <p>
            Electrical outlets and one charcoal grill are available at each shelter house. (The Crown Pavilion does not have charcoal grills).
            √ Renter is responsible for any damages (shelter houses are inspected daily).
            I, the undersigned, have read, understand and agree to the attached and listed above rules.</p>
    @endif
</body>
</html>