<script setup lang="ts">
import { usePage, Link } from '@inertiajs/vue3'
import NavFooter from '@/components/NavFooter.vue'
import NavMain from '@/components/NavMain.vue'
import NavUser from '@/components/NavUser.vue'
import { Sidebar, SidebarContent, SidebarFooter, SidebarGroup, SidebarGroupLabel, SidebarHeader, SidebarMenu, SidebarMenuButton, SidebarMenuItem } from '@/components/ui/sidebar'
import { Collapsible, CollapsibleTrigger, CollapsibleContent } from '@/components/ui/collapsible'
import { type NavItem } from '@/types'
import { Copyright, Folder, LayoutGrid,Paperclip, ChevronDown, Landmark, IdCard, Settings as SettingsIcon, Shield, User,KeyRound } from 'lucide-vue-next'
import AppLogo from './AppLogo.vue'

const page = usePage()
const user = page.props.auth.user

// menu utama
const mainNavItems: NavItem[] = [
  {
    title: 'DASHBOARD',
    href: '/dashboard',
    icon: LayoutGrid,
  },
  {
    title: 'Import Kredit',
    href: '/import-kredit',
    icon: Paperclip,
  },
]

// menu master hanya untuk admin
const masterNavItems: NavItem[] = [
  {
    title: 'Kantor',
    href: '/kantor',
    icon: Landmark,
  },
  {
    title: 'Divisi',
    href: '/divisi',
    icon: IdCard,
  },
  {
    title: 'Role',
    href: '/roles',
    icon: Shield,
  },
  {
    title: 'Permission',
    href: '/permissions',
    icon: KeyRound,
  },
  {
    title: 'User',
    href: '/users',
    icon: User,
  },
]

// menu footer
const footerNavItems: NavItem[] = [
//   {
//     title: 'Github Repo',
//     href: 'https://github.com/laravel/vue-starter-kit',
//     icon: Folder,
//   },
  {
    title: 'SUGA@2025',
    href: 'https://bprku.com',
    icon: Copyright,
  },
]
</script>

<template>
  <Sidebar collapsible="icon" variant="inset">
    <!-- Header -->
    <SidebarHeader>
      <SidebarMenu>
        <SidebarMenuItem>
          <SidebarMenuButton size="lg" as-child>
            <Link :href="route('dashboard')">
              <AppLogo />
            </Link>
          </SidebarMenuButton>
        </SidebarMenuItem>
      </SidebarMenu>
    </SidebarHeader>

    <!-- Content -->
    <SidebarContent>
      <NavMain :items="mainNavItems" />

      <!-- Setting menu hanya muncul kalau role admin -->
      <Collapsible
        v-if="user?.roles?.includes('admin')"
        defaultOpen
        class="group/collapsible"
      >
        <SidebarGroup>
          <SidebarGroupLabel asChild>
            <CollapsibleTrigger class="flex items-center gap-2">
              <SettingsIcon class="h-4 w-4" />
              SETTING
              <ChevronDown
                class="ml-auto transition-transform group-data-[state=open]/collapsible:rotate-180"
              />
            </CollapsibleTrigger>
          </SidebarGroupLabel>
          <CollapsibleContent>
            <NavMain :items="masterNavItems" />
          </CollapsibleContent>
        </SidebarGroup>
      </Collapsible>
    </SidebarContent>

    <!-- Footer -->
    <SidebarFooter>
      <NavFooter :items="footerNavItems" />
      <NavUser />
    </SidebarFooter>
  </Sidebar>

  <slot />
</template>
