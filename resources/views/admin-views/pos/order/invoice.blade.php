<div class="w-410px">
    <div class="text-center pt-4 mb-3">
        <h2 class="text-break lh-1">{{$order->restaurant->name}}</h2>
        <h5 class="text-break initial-44">
            {{$order->restaurant->address}}
        </h5>
        <h5 class="initial-45">
            {{ translate('messages.phone') }} : {{$order->restaurant->phone}}
        </h5>
        @if($order->restaurant->gst_status)
        <h5 class="initial-46">
            {{ translate('messages.Gst No') }} : {{$order->restaurant->gst_code}}
        </h5>
        @endif
    </div>

    <span>---------------------------------------------------------------------------------</span>
    <div class="row mt-3">
        <div class="col-6">
            <h5>{{ translate('messages.Order ID') }} : {{$order['id']}}</h5>
        </div>
        <div class="col-6">
            <h5 class="font-light">
                {{date('d/M/Y '.config('timeformat'),strtotime($order['created_at']))}}
            </h5>
        </div>
        @if($order->customer)
        <div class="col-12 text-break">
            <h5>
                {{ translate('messages.customer_name') }} : {{$order->customer['f_name'].' '.$order->customer['l_name']}}
            </h5>
            <h5>
                {{ translate('messages.phone') }} : {{$order->customer['phone']}}
            </h5>
            <h5 class="text-break">
                {{ translate('messages.Address') }} : {{isset($order->delivery_address)?json_decode($order->delivery_address, true)['address']:''}}
            </h5>
        </div>
        @endif
    </div>
    <h5 class="text-uppercase"></h5>
    <span>---------------------------------------------------------------------------------</span>
    <table class="table table-bordered mt-3 w-100">
        <thead>
        <tr>
            <th class="w-10px">{{ translate('messages.Qty') }}</th>
            <th class="">{{ translate('messages.description') }}</th>
            <th class="">{{ translate('Price') }}</th>
        </tr>
        </thead>

        <tbody>
        @php($zone_currency=$order->zone_currency ?? null)
        @php($sub_total=0)
        @php($total_tax=0)
        @php($total_dis_on_pro=0)
        @php($add_ons_cost=0)
        @foreach($order->details as $detail)
            @if($detail->food)
                <tr>
                    <td class="">
                        {{$detail['quantity']}}
                    </td>
                    <td class="text-break">
                        {{$detail->food['name']}} <br>
                        @if(isset(json_decode($detail['variation'],true)[0]))
                            <strong><u>{{ translate('Variation') }} : </u></strong>
                            @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                <div class="font-size-sm text-body">
                                    <span>{{$key1}} :  </span>
                                    <span class="font-weight-bold">{{$key1=='price'?\App\CentralLogics\Helpers::format_currency($variation ,$zone_currency):$variation}}</span>
                                </div>
                            @endforeach
                        @else
                        <div class="font-size-sm text-body">
                            <span>{{'Price'}} :  </span>
                            <span class="font-weight-bold">{{\App\CentralLogics\Helpers::format_currency($detail->price,$zone_currency)}}</span>
                        </div>
                        @endif

                        @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                            @if($key2==0)<strong><u>{{ translate('Addons') }} : </u></strong>@endif
                            <div class="font-size-sm text-body">
                                <span class="text-break">{{$addon['name']}} :  </span>
                                <span class="font-weight-bold">
                                    {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'],$zone_currency)}}
                                </span>
                            </div>
                            @php($add_ons_cost+=$addon['price']*$addon['quantity'])
                        @endforeach
                    </td>
                    <td class="w-28p">
                        @php($amount=($detail['price'])*$detail['quantity'])
                        {{\App\CentralLogics\Helpers::format_currency($amount,$zone_currency)}}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])

            @elseif($detail->campaign)
                <tr>
                    <td class="">
                        {{$detail['quantity']}}
                    </td>
                    <td class="text-break">
                        {{$detail->campaign['title']}} <br>
                        @if(isset(json_decode($detail['variation'],true)[0]))
                            <strong><u>{{ translate('Variation') }} : </u></strong>
                            @foreach(json_decode($detail['variation'],true)[0] as $key1 =>$variation)
                                <div class="font-size-sm text-body">
                                    <span>{{$key1}} :  </span>
                                    <span class="font-weight-bold">{{$key1=='price'?\App\CentralLogics\Helpers::format_currency($variation,$zone_currency):$variation}}</span>
                                </div>
                            @endforeach
                        @else
                        <div class="font-size-sm text-body">
                            <span>{{'Price'}} :  </span>
                            <span class="font-weight-bold">{{\App\CentralLogics\Helpers::format_currency($detail->price,$zone_currency)}}</span>
                        </div>
                        @endif

                        @foreach(json_decode($detail['add_ons'],true) as $key2 =>$addon)
                            @if($key2==0)<strong><u>{{ translate('Addons') }} : </u></strong>@endif
                            <div class="font-size-sm text-body">
                                <span class="text-break">{{$addon['name']}} :  </span>
                                <span class="font-weight-bold">
                                                {{$addon['quantity']}} x {{\App\CentralLogics\Helpers::format_currency($addon['price'],$zone_currency)}}
                                            </span>
                            </div>
                            @php($add_ons_cost+=$addon['price']*$addon['quantity'])
                        @endforeach
                    </td>
                    <td class="w-28p">
                        @php($amount=($detail['price'])*$detail['quantity'])
                        {{\App\CentralLogics\Helpers::format_currency($amount,$zone_currency)}}
                    </td>
                </tr>
                @php($sub_total+=$amount)
                @php($total_tax+=$detail['tax_amount']*$detail['quantity'])
            @endif
        @endforeach
        </tbody>
    </table>
    <span>---------------------------------------------------------------------------------</span>
    <div class="row justify-content-md-end">
        <div class="col-md-7 col-lg-7">
            <dl class="row text-right">
                <dt class="col-6">{{ translate('Items Price') }}:</dt>
                <dd class="col-6">{{\App\CentralLogics\Helpers::format_currency($sub_total,$zone_currency)}}</dd>
                <dt class="col-6">{{ translate('Addon Cost') }}:</dt>
                <dd class="col-6">
                    {{\App\CentralLogics\Helpers::format_currency($add_ons_cost,$zone_currency)}}
                    <hr>
                </dd>
                <dt class="col-6">{{ translate('Subtotal') }}:</dt>
                <dd class="col-6">
                    {{\App\CentralLogics\Helpers::format_currency($sub_total+$total_tax+$add_ons_cost),$zone_currency}}</dd>
                <dt class="col-6">{{translate('messages.discount')}}:</dt>
                <dd class="col-6">
                    - {{\App\CentralLogics\Helpers::format_currency($order['restaurant_discount_amount'],$zone_currency)}}</dd>
                <dt class="col-6">{{ translate('Coupon Discount') }}:</dt>
                <dd class="col-6">
                    - {{\App\CentralLogics\Helpers::format_currency($order['coupon_discount_amount'],$zone_currency)}}</dd>
                <dt class="col-6">{{translate('messages.vat/tax')}}:</dt>
                <dd class="col-6">+ {{\App\CentralLogics\Helpers::format_currency($order['total_tax_amount'],$zone_currency)}}</dd>
                <dt class="col-6">{{ translate('Delivery Fee') }}:</dt>
                <dd class="col-6">
                    @php($del_c=$order['delivery_charge'])
                    {{\App\CentralLogics\Helpers::format_currency($del_c,$zone_currency)}}
                    <hr>
                </dd>

                <dt class="col-6 fz-20px">{{ translate('Total') }}:</dt>
                <dd class="col-6 fz-20px">{{\App\CentralLogics\Helpers::format_currency($sub_total+$del_c+$order['total_tax_amount']+$add_ons_cost-$order['coupon_discount_amount'] - $order['restaurant_discount_amount'],$zone_currency)}}</dd>
            </dl>
        </div>
    </div>
    {{-- <div class="d-flex flex-row justify-content-between border-top">
        <span>Payment Method: {{($order->payment_method)}}</span>	<span>Amount: {{ abs($order->adjusment)}}</span>	<span>Change: {{ abs($order->order_amount + $order->adjusment)}}</span>
    </div> --}}
    <span>---------------------------------------------------------------------------------</span>
    <h5 class="text-center pt-3">
        """{{ translate('THANK YOU') }}"""
    </h5>
    <span>---------------------------------------------------------------------------------</span>
</div>
