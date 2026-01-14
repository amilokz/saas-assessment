<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Plan;
use Illuminate\Http\Request;

class PlanController extends Controller
{
    public function index()
    {
        $plans = Plan::orderBy('sort_order')->get();
        return view('super-admin.plans', compact('plans'));
    }

    public function create()
    {
        return view('super-admin.plans-create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:plans,slug',
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'max_users' => 'nullable|integer|min:0',
            'max_files' => 'nullable|integer|min:0',
            'max_storage_mb' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($validated['features'])) {
            $validated['features'] = json_encode($validated['features']);
        }

        Plan::create($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan created successfully.');
    }

    public function edit(Plan $plan)
    {
        return view('super-admin.plans-edit', compact('plan'));
    }

    public function update(Request $request, Plan $plan)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|unique:plans,slug,' . $plan->id,
            'description' => 'nullable|string',
            'monthly_price' => 'required|numeric|min:0',
            'yearly_price' => 'required|numeric|min:0',
            'max_users' => 'nullable|integer|min:0',
            'max_files' => 'nullable|integer|min:0',
            'max_storage_mb' => 'nullable|integer|min:0',
            'features' => 'nullable|array',
            'is_active' => 'boolean',
            'is_trial' => 'boolean',
            'trial_days' => 'nullable|integer|min:0',
            'sort_order' => 'nullable|integer',
        ]);

        if (isset($validated['features'])) {
            $validated['features'] = json_encode($validated['features']);
        }

        $plan->update($validated);

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan updated successfully.');
    }

    public function destroy(Plan $plan)
    {
        if ($plan->subscriptions()->exists()) {
            return redirect()->back()
                ->with('error', 'Cannot delete plan with active subscriptions.');
        }

        $plan->delete();

        return redirect()->route('super-admin.plans.index')
            ->with('success', 'Plan deleted successfully.');
    }

    public function toggleStatus(Plan $plan)
    {
        $plan->update([
            'is_active' => !$plan->is_active,
        ]);

        $status = $plan->is_active ? 'activated' : 'deactivated';
        
        return redirect()->back()
            ->with('success', "Plan {$status} successfully.");
    }
}