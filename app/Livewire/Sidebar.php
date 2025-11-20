<?php

namespace App\Livewire;

use Livewire\Component;

class Sidebar extends Component
{
    public bool $isCollapsed = false;
    public bool $mobileOpen = false;
    public $openSubmenu = null;

    public function toggle(): void
    {
        $this->isCollapsed = !$this->isCollapsed;
    }

    public function toggleMobile(): void
    {
        $this->mobileOpen = !$this->mobileOpen;
    }

    public function closeMobile(): void
    {
        $this->mobileOpen = false;
    }

    public function toggleSubmenu($id): void
    {
        $this->openSubmenu = $this->openSubmenu === $id ? null : $id;
    }

    public function logout()
    {
        auth()->logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }

    public function render()
    {
        $user = auth()->user()?->loadMissing(['persona.institucion', 'institucion', 'role']);
        $abilities = [
            'manageUsers' => $user?->canManageUsers() ?? false,
            'manageInstitutions' => $user?->isAdmin() ?? false,
        ];

        return view('livewire.sidebar', [
            'user' => $user,
            'abilities' => $abilities,
        ]);
    }
}
