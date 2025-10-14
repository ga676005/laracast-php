<?php

use Core\Router;
use Core\Session;

// Function to get the current page from the URI
function getCurrentPage()
{
    // Get the request URI and strip project directory
    $uri = parse_url($_SERVER['REQUEST_URI'])['path'];

    // Remove project directory (same logic as router)
    $scriptDir = dirname($_SERVER['SCRIPT_NAME']);
    $projectDir = $scriptDir === '/' ? '' : $scriptDir;

    if ($projectDir && str_starts_with($uri, $projectDir)) {
        $uri = substr($uri, strlen($projectDir));
    }

    // Convert URI to page name
    switch ($uri) {
        case '/':
        case '/home':
            return 'index';
        case '/about':
            return 'about';
        case '/contact':
            return 'contact';
        default:
            return '';
    }
}

// Function to check if a page is active
function isActivePage($pageName)
{
    return getCurrentPage() === $pageName;
}

// Function to get CSS classes for navigation links
function getNavClasses($pageName)
{
    $baseClasses = 'rounded-md px-3 py-2 text-sm font-medium';

    if (isActivePage($pageName)) {
        return $baseClasses . ' bg-gray-900 text-white';
    } else {
        return $baseClasses . ' text-gray-300 hover:bg-white/5 hover:text-white';
    }
}
?>

<nav class="bg-gray-800">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="flex h-16 items-center justify-between">
            <div class="flex items-center">
                <div class="shrink-0">
                    <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company" class="size-8" />
                </div>
                <div class="hidden md:block">
                    <div class="ml-10 flex items-baseline space-x-4">
                        <a href="<?= Router::url('/') ?>" <?= isActivePage('index') ? 'aria-current="page"' : '' ?> class="<?= getNavClasses('index') ?>">Home</a>
                        <a href="<?= Router::url('/about') ?>" <?= isActivePage('about') ? 'aria-current="page"' : '' ?> class="<?= getNavClasses('about') ?>">About</a>
                        <a href="<?= Router::url('/notes') ?>" <?= isActivePage('notes') ? 'aria-current="page"' : '' ?> class="<?= getNavClasses('notes') ?>">Notes</a>
                        <a href="<?= Router::url('/contact') ?>" <?= isActivePage('contact') ? 'aria-current="page"' : '' ?> class="<?= getNavClasses('contact') ?>">Contact</a>
                    </div>
                </div>
            </div>
            <div class="hidden md:block">
                <div class="ml-4 flex items-center md:ml-6">
                    <button type="button" class="relative rounded-full p-1 text-gray-400 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
                        <span class="absolute -inset-1.5"></span>
                        <span class="sr-only">View notifications</span>
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                            <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
                        </svg>
                    </button>

                    <!-- Profile dropdown -->
                    <?php if (Session::isLoggedIn()): ?>
                    <?php $user = Session::getUser(); ?>
                    <el-dropdown class="relative ml-3">
                        <button class="relative flex max-w-xs items-center rounded-full focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                            <span class="absolute -inset-1.5"></span>
                            <span class="sr-only">Open user menu</span>
                            <div class="size-8 rounded-full bg-white text-gray-800 flex items-center justify-center font-semibold text-sm outline -outline-offset-1 outline-white/10">
                                <?= strtoupper(substr($user['email'], 0, 1)) ?>
                            </div>
                        </button>

                        <el-menu anchor="bottom end" popover class="w-48 origin-top-right rounded-md bg-white py-1 shadow-lg outline-1 outline-black/5 transition transition-discrete [--anchor-gap:--spacing(2)] data-closed:scale-95 data-closed:transform data-closed:opacity-0 data-enter:duration-100 data-enter:ease-out data-leave:duration-75 data-leave:ease-in">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:outline-hidden">Your profile</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:outline-hidden">Settings</a>
                            <a href="<?= Router::url('/signout') ?>" class="block px-4 py-2 text-sm text-gray-700 focus:bg-gray-100 focus:outline-hidden">Sign out</a>
                        </el-menu>
                    </el-dropdown>
                    <?php else: ?>
                    <a href="<?= Router::url('/signin') ?>" class="rounded-md px-3 py-2 text-sm font-medium text-gray-300 hover:bg-white/5 hover:text-white">
                        Sign In
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="-mr-2 flex md:hidden">
                <!-- Mobile menu button -->
                <button type="button" command="--toggle" commandfor="mobile-menu" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-white/5 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
                    <span class="absolute -inset-0.5"></span>
                    <span class="sr-only">Open main menu</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 in-aria-expanded:hidden">
                        <path d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 not-in-aria-expanded:hidden">
                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <el-disclosure id="mobile-menu" hidden class="block md:hidden">
        <div class="space-y-1 px-2 pt-2 pb-3 sm:px-3">
            <a href="<?= Router::url('/') ?>" <?= isActivePage('index') ? 'aria-current="page"' : '' ?> class="block <?= getNavClasses('index') ?>">Home</a>
            <a href="<?= Router::url('/about') ?>" <?= isActivePage('about') ? 'aria-current="page"' : '' ?> class="block <?= getNavClasses('about') ?>">About</a>
            <a href="<?= Router::url('/notes') ?>" <?= isActivePage('notes') ? 'aria-current="page"' : '' ?> class="block <?= getNavClasses('notes') ?>">Notes</a>
            <a href="<?= Router::url('/contact') ?>" <?= isActivePage('contact') ? 'aria-current="page"' : '' ?> class="block <?= getNavClasses('contact') ?>">Contact</a>
        </div>
        <div class="border-t border-white/10 pt-4 pb-3">
            <?php if (Session::isLoggedIn()): ?>
            <?php $user = Session::getUser(); ?>
            <div class="flex items-center px-5">
                <div class="shrink-0">
                    <div class="size-10 rounded-full bg-white text-gray-800 flex items-center justify-center font-semibold text-lg outline -outline-offset-1 outline-white/10">
                        <?= strtoupper(substr($user['email'], 0, 1)) ?>
                    </div>
                </div>
                <div class="ml-3">
                    <div class="text-base/5 font-medium text-white"><?= $user['email'] ?></div>
                </div>
                <button type="button" class="relative ml-auto shrink-0 rounded-full p-1 text-gray-400 hover:text-white focus:outline-2 focus:outline-offset-2 focus:outline-indigo-500">
                    <span class="absolute -inset-1.5"></span>
                    <span class="sr-only">View notifications</span>
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6">
                        <path d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                </button>
            </div>
            <div class="mt-3 space-y-1 px-2">
                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">Your profile</a>
                <a href="#" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">Settings</a>
                <a href="<?= Router::url('/signout') ?>" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">Sign out</a>
            </div>
            <?php else: ?>
            <div class="px-5">
                <a href="<?= Router::url('/signin') ?>" class="block rounded-md px-3 py-2 text-base font-medium text-gray-400 hover:bg-white/5 hover:text-white">
                    Sign In
                </a>
            </div>
            <?php endif; ?>
        </div>
    </el-disclosure>
</nav>