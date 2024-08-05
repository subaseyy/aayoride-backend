<?php

namespace Modules\UserManagement\Service;


use App\Service\BaseService;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Modules\TransactionManagement\Traits\TransactionTrait;
use Modules\UserManagement\Repository\WithdrawMethodRepositoryInterface;
use Modules\UserManagement\Repository\WithdrawRequestRepositoryInterface;
use Modules\UserManagement\Service\Interface\WithdrawRequestServiceInterface;

class WithdrawRequestService extends BaseService implements WithdrawRequestServiceInterface
{
    use TransactionTrait;

    protected $withdrawRequestRepository;

    public function __construct(WithdrawRequestRepositoryInterface $withdrawRequestRepository)
    {
        parent::__construct($withdrawRequestRepository);
        $this->withdrawRequestRepository = $withdrawRequestRepository;
    }

    public function update(int|string $id, array $data = []): ?Model
    {
        $withdrawRequest = $this->withdrawRequestRepository->findOne(id: $id, relations: ['user' => []]);
        if (array_key_exists('rejection_cause', $data) && !is_null($data['rejection_cause'])) {
            $attributes['rejection_cause'] = $data['rejection_cause'];
        }
        if (array_key_exists('approval_note', $data) && !is_null($data['approval_note'])) {
            $attributes['approval_note'] = $data['approval_note'];
        }
        if (array_key_exists('denied_note', $data) && !is_null($data['denied_note'])) {
            $attributes['denied_note'] = $data['denied_note'];
        }
        if ($data['status'] == DENIED) {
            $this->withdrawRequestCancelTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
        }
        if ($data['status'] == SETTLED) {
            $this->withdrawRequestAcceptTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
        }
        if ($data['status'] == 'reverse') {
            $this->withdrawRequestReverseTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
        }
        if ($data['status'] == 'reverse') {
            $attributes['status'] = PENDING;
            $this->withdrawRequestRepository->update(id: $id, data: $attributes);
        }
        if ($withdrawRequest->status == PENDING && $data['status'] == APPROVED) {
            $attributes['status'] = APPROVED;
            $this->withdrawRequestRepository->update(id: $id, data: $attributes);
        }
        if ($withdrawRequest->status == PENDING && $data['status'] == DENIED) {
            $attributes['status'] = DENIED;
            $this->withdrawRequestRepository->update(id: $id, data: $attributes);
        }
        if ($withdrawRequest->status == APPROVED && $data['status'] == SETTLED) {
            $attributes['status'] = SETTLED;
            $this->withdrawRequestRepository->update(id: $id, data: $attributes);
        }
        $withdrawRequestData = $this->withdrawRequestRepository->findOne(id: $id, relations: ['user' => []]);

        if ($data['status'] == DENIED) {
            sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                title: translate('withdraw_request_rejected'),
                description: translate(('admin_has_rejected_your_withdraw_request' . ($withdrawRequestData?->denied_note != null ? ', because ' . $withdrawRequestData?->denied_note : ' .'))),
                action: 'withdraw_rejected',
                user_id: $withdrawRequestData?->user->id
            );
        } elseif ($data['status'] == SETTLED) {
            sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                title: translate('withdraw_request_settled'),
                description: translate('admin_has_settled_your_withdraw_request'),
                action: 'withdraw_settled',
                user_id: $withdrawRequestData?->user->id
            );
        } elseif ($data['status'] == APPROVED) {
            sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                title: translate('withdraw_request_approved'),
                description: translate(('admin_has_approved_your_withdraw_request' . ($withdrawRequestData?->approved_note != null ? ', because ' . $withdrawRequestData?->approved_note : ' .'))),
                action: 'withdraw_approved',
                user_id: $withdrawRequestData?->user->id
            );
        } else {
            sendDeviceNotification(fcm_token: $withdrawRequestData?->user->fcm_token,
                title: translate('withdraw_request_reversed'),
                description: translate('admin_has_reversed_your_withdraw_request'),
                action: 'withdraw_reversed',
                user_id: $withdrawRequestData?->user->id
            );
        }
        return $withdrawRequestData;
    }

    public function multipleUpdate(array $data = []): void
    {
        if (array_key_exists('status', $data) && !is_null($data['status']) && array_key_exists('ids', $data) && (count($data['ids']) > 0)) {
            foreach ($data['ids'] as $id) {
                $withdrawRequest = $this->withdrawRequestRepository->findOne(id: $id, relations: ['user' => []]);
                if ($data['status'] == 'reverse') {
                    $attributes['status'] = PENDING;
                } else {
                    $attributes['status'] = $data['status'];
                }

                if (array_key_exists('rejection_cause', $data) && !is_null($data['rejection_cause'])) {
                    $attributes['rejection_cause'] = $data['rejection_cause'];
                }
                if (array_key_exists('approval_note', $data) && !is_null($data['approval_note'])) {
                    $attributes['approval_note'] = $data['approval_note'];
                }
                if (array_key_exists('denied_note', $data) && !is_null($data['denied_note'])) {
                    $attributes['denied_note'] = $data['denied_note'];
                }
                DB::beginTransaction();
                if ($data['status'] == DENIED) {
                    $this->withdrawRequestCancelTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
                }
                if ($data['status'] == SETTLED) {
                    $this->withdrawRequestAcceptTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
                }
                if ($data['status'] == 'reverse') {
                    $this->withdrawRequestReverseTransaction($withdrawRequest->user, $withdrawRequest->amount, $withdrawRequest);
                }
                $this->withdrawRequestRepository->update(id: $id, data: $attributes);
                DB::commit();
                $withdrawRequestData = $this->withdrawRequestRepository->findOne(id: $id, relations: ['user' => []]);

                if ($data['status'] == DENIED) {
                    sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                        title: translate('withdraw_request_rejected'),
                        description: translate(('admin_has_rejected_your_withdraw_request' . ($withdrawRequestData?->denied_note != null ? ', because ' . $withdrawRequestData?->denied_note : ' .'))),
                        action: 'withdraw_rejected',
                        user_id: $withdrawRequestData?->user->id
                    );
                } elseif ($data['status'] == SETTLED) {
                    sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                        title: translate('withdraw_request_settled'),
                        description: translate('admin_has_settled_your_withdraw_request'),
                        action: 'withdraw_settled',
                        user_id: $withdrawRequestData?->user->id
                    );
                } elseif ($data['status'] == APPROVED) {
                    sendDeviceNotification(fcm_token: $withdrawRequestData->user->fcm_token,
                        title: translate('withdraw_request_approved'),
                        description: translate(('admin_has_approved_your_withdraw_request' . ($withdrawRequestData?->approved_note != null ? ', because ' . $withdrawRequestData?->approved_note : ' .'))),
                        action: 'withdraw_approved',
                        user_id: $withdrawRequestData?->user->id
                    );
                } else {
                    sendDeviceNotification(fcm_token: $withdrawRequestData?->user->fcm_token,
                        title: translate('withdraw_request_reversed'),
                        description: translate('admin_has_reversed_your_withdraw_request'),
                        action: 'withdraw_reversed',
                        user_id: $withdrawRequestData?->user->id
                    );
                }
            }
        }
    }



}
