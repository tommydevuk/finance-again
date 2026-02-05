<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntityInvitationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        $entity = $this->route('entity');

        if (! $entity) {
            return false;
        }

        // Set context for Spatie Permissions
        app(\Spatie\Permission\PermissionRegistrar::class)->setPermissionsTeamId($entity->id);

        return $this->user()->hasPermissionTo('update entity');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'role' => ['required', 'string', 'max:255'],
            'project_id' => ['nullable', 'exists:projects,id'],
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->project_id) {
                $project = \App\Models\Project::find($this->project_id);
                if ($project && $project->entity_id !== $this->route('entity')->id) {
                    $validator->errors()->add('project_id', 'The selected project does not belong to this team.');
                }
            }
        });
    }
}