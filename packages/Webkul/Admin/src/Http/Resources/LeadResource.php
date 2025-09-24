<?php

namespace Webkul\Admin\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class LeadResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id'                          => $this->id,
            'title'                       => $this->title,
            'client_company'              => $this->client_company,
            'owner_company'               => $this->owner_company,
            'opportunity_number'          => $this->opportunity_number,
            'proposal_due_date'           => $this->proposal_due_date,
            'formatted_proposal_due_date' => $this->proposal_due_date
                ? Carbon::parse($this->proposal_due_date)->format(config('app.date_format', 'M j, Y'))
                : null,
            'lead_value'                  => $this->lead_value,
            'formatted_lead_value'        => core()->formatBasePrice($this->lead_value),
            'status'                      => $this->status,
            'expected_close_date'         => $this->expected_close_date,
            'rotten_days'                 => $this->rotten_days,
            'closed_at'                   => $this->closed_at,
            'created_at'                  => $this->created_at,
            'updated_at'                  => $this->updated_at,
            'person'                      => new PersonResource($this->person),
            'user'                        => new UserResource($this->user),
            'type'                        => new TypeResource($this->type),
            'source'                      => new SourceResource($this->source),
            'pipeline'                    => new PipelineResource($this->pipeline),
            'stage'                       => new StageResource($this->stage),
            'tags'                        => TagResource::collection($this->tags),
        ];
    }
}