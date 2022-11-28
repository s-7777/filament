<?php

namespace Filament\Pages;

use Filament\Facades\Filament;
use Filament\Navigation\NavigationItem;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;

abstract class Page extends BasePage
{
    use Concerns\HasRoutes;
    use Concerns\InteractsWithHeaderActions;

    protected static bool $isDiscovered = true;

    protected static string $layout = 'filament::components.layouts.app';

    protected static ?string $navigationGroup = null;

    protected static ?string $navigationIcon = null;

    protected static ?string $activeNavigationIcon = null;

    protected static ?string $navigationLabel = null;

    protected static ?int $navigationSort = null;

    protected static bool $shouldRegisterNavigation = true;

    public static string $formActionsAlignment = 'left';

    public static bool $hasInlineFormLabels = false;

    /**
     * @param array<mixed> $parameters
     */
    public static function getUrl(array $parameters = [], bool $isAbsolute = true, ?string $context = null, ?Model $tenant = null): string
    {
        $parameters['tenant'] ??= ($tenant ?? Filament::getRoutableTenant());

        return route(static::getRouteName($context), $parameters, $isAbsolute);
    }

    public static function registerNavigationItems(): void
    {
        if (! static::shouldRegisterNavigation()) {
            return;
        }

        Filament::getCurrentContext()
            ->navigationItems(static::getNavigationItems());
    }

    /**
     * @return array<NavigationItem>
     */
    public static function getNavigationItems(): array
    {
        return [
            NavigationItem::make(static::getNavigationLabel())
                ->group(static::getNavigationGroup())
                ->icon(static::getNavigationIcon())
                ->isActiveWhen(fn (): bool => request()->routeIs(static::getRouteName()))
                ->sort(static::getNavigationSort())
                ->badge(static::getNavigationBadge(), color: static::getNavigationBadgeColor())
                ->url(static::getNavigationUrl()),
        ];
    }

    public static function getRouteName(?string $context = null): string
    {
        $context ??= Filament::getCurrentContext()->getId();

        $slug = static::getSlug();

        return "filament.{$context}.pages.{$slug}";
    }

    /**
     * @return array<string>
     */
    public function getBreadcrumbs(): array
    {
        return [];
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function getActiveNavigationIcon(): string
    {
        return static::$activeNavigationIcon ?? static::getNavigationIcon();
    }

    public static function getNavigationIcon(): string
    {
        return static::$navigationIcon ?? 'heroicon-o-document-text';
    }

    public static function getNavigationLabel(): string
    {
        return static::$navigationLabel ?? static::$title ?? str(class_basename(static::class))
            ->kebab()
            ->replace('-', ' ')
            ->title();
    }

    public static function getNavigationBadge(): ?string
    {
        return null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return null;
    }

    public static function getNavigationSort(): ?int
    {
        return static::$navigationSort;
    }

    public static function getNavigationUrl(): string
    {
        return static::getUrl();
    }

    public function getFooter(): ?View
    {
        return null;
    }

    public function getHeader(): ?View
    {
        return null;
    }

    /**
     * @return array<class-string>
     */
    protected function getHeaderWidgets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public function getVisibleHeaderWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getHeaderWidgets());
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getHeaderWidgetsColumns(): int | array
    {
        return 2;
    }

    /**
     * @return array<class-string>
     */
    protected function getFooterWidgets(): array
    {
        return [];
    }

    /**
     * @return array<class-string>
     */
    public function getVisibleFooterWidgets(): array
    {
        return $this->filterVisibleWidgets($this->getFooterWidgets());
    }

    /**
     * @param array<class-string> $widgets
     * @return array<class-string>
     */
    protected function filterVisibleWidgets(array $widgets): array
    {
        return array_filter($widgets, fn (string $widget): bool => $widget::canView());
    }

    /**
     * @return int | array<string, int | null>
     */
    public function getFooterWidgetsColumns(): int | array
    {
        return 2;
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::$shouldRegisterNavigation;
    }

    public static function alignFormActionsLeft(): void
    {
        static::$formActionsAlignment = 'left';
    }

    public static function alignFormActionsCenter(): void
    {
        static::$formActionsAlignment = 'center';
    }

    public static function alignFormActionsRight(): void
    {
        static::$formActionsAlignment = 'right';
    }

    public function getFormActionsAlignment(): string
    {
        return static::$formActionsAlignment;
    }

    public function hasInlineFormLabels(): bool
    {
        return static::$hasInlineFormLabels;
    }

    public static function isDiscovered(): bool
    {
        return static::$isDiscovered;
    }
}