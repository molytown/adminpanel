@foreach($campaigns as $key=>$campaign)
<tr>
    <td>{{$key+1}}</td>
    <td>
        <span class="d-block text-body">{{Str::limit($campaign['title'],25,'...')}}
        </span>
    </td>
    <td>

        <span class="bg-gradient-light text-dark">{{$campaign->start_date?  \App\CentralLogics\Helpers::date_format($campaign->start_date)  : 'N/A'}}</span>

        <span class="bg-gradient-light text-dark">-</span>

        <span class="bg-gradient-light text-dark">{{$campaign->start_time?  \App\CentralLogics\Helpers::date_format($campaign->end_date) : 'N/A' }}</span>
    </td>
    <td>

        <span class="bg-gradient-light text-dark">{{$campaign->start_time?  \App\CentralLogics\Helpers::time_format($campaign->start_time). ' - ' .\App\CentralLogics\Helpers::time_format($campaign->end_time): 'N/A'}}</span>

    </td>
    <td>{{$campaign->price}}</td>
    {{-- <td>
        <label class="toggle-switch toggle-switch-sm" for="campaignCheckbox{{$campaign->id}}">
            <input type="checkbox" onclick="location.href='{{route('admin.campaign.status',['item',$campaign['id'],$campaign->status?0:1])}}'"class="toggle-switch-input" id="campaignCheckbox{{$campaign->id}}" {{$campaign->status?'checked':''}}>
            <span class="toggle-switch-label">
                <span class="toggle-switch-indicator"></span>
            </span>
        </label>
    </td>
    <td>
        <div class="btn--container justify-content-center">
            <a class="btn btn-sm btn--primary btn-outline-primary action-btn"
                href="{{route('admin.campaign.edit',['item',$campaign['id']])}}" title="{{translate('messages.edit_campaign')}}"><i class="tio-edit"></i>
            </a>
            <a class="btn btn-sm btn--danger btn-outline-danger action-btn" href="javascript:"
                onclick="form_alert('campaign-{{$campaign['id']}}','Want to delete this item ?')" title="{{translate('messages.delete_campaign')}}"><i class="tio-delete-outlined"></i>
            </a>
        </div>
        <form action="{{route('admin.campaign.delete-item',[$campaign['id']])}}"
                      method="post" id="campaign-{{$campaign['id']}}">
            @csrf @method('delete')
        </form>
    </td> --}}
</tr>
@endforeach
