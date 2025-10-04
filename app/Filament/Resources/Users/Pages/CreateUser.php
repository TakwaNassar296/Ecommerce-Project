<?php

namespace App\Filament\Resources\Users\Pages;

use App\Models\User;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\Users\UserResource;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $existingUser = User::withoutGlobalScopes()
        ->where('email', $data['email'])->first();

        if ($existingUser) {
            $admin = auth('admin')->user();
            if ($admin && $admin->tenant_id) {
                $existingUser->tenants()->syncWithoutDetaching([$admin->tenant_id]);
            }

            $this->redirect(\App\Filament\Resources\Users\UserResource::getUrl('edit', ['record' => $existingUser]));

            $this->halt();
        }

        return $data;
    }
}
