<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import TextLink from '@/components/TextLink.vue';
import { Button } from '@/components/ui/button';
import { Checkbox } from '@/components/ui/checkbox';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import AuthBase from '@/layouts/AuthLayout.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { LoaderCircle } from 'lucide-vue-next';
import { computed } from 'vue';

defineProps<{ 
    status?: string; 
    canResetPassword: boolean; 
}>();

const page = usePage();
const csrfToken = computed(() => page.props.csrf_token);

const form = useForm({
    username: '',
    password: '',
    remember: false,
    _token: csrfToken.value,
});

const submit = () => {
    // Update token sebelum submit
    form._token = csrfToken.value;
    
    form.post(route('login'), {
        headers: {
            'X-CSRF-TOKEN': csrfToken.value,
            'X-Requested-With': 'XMLHttpRequest',
        },
        onFinish: () => {
            form.reset('password');
        },
        onError: (errors) => {
            console.error('Login error:', errors);
            // Jika error 419, reload untuk mendapatkan token baru
            if (errors.response?.status === 419) {
                window.location.reload();
            }
        },
        onSuccess: () => {
            console.log('Login successful');
        },
    });
};
</script>

<template>
    <AuthBase title="Log in to your account" description="Enter your username and password below to log in">
        <Head title="Log in" />
        
        <div v-if="status" class="mb-4 text-center text-sm font-medium text-green-600">
            {{ status }}
        </div>
        
        <form @submit.prevent="submit" class="flex flex-col gap-6">
            <input type="hidden" name="_token" :value="csrfToken">
            
            <div class="grid gap-6">
                <div class="grid gap-2">
                    <Label for="username">Username</Label>
                    <Input 
                        id="username" 
                        type="text" 
                        required 
                        autofocus 
                        :tabindex="1" 
                        autocomplete="username" 
                        v-model="form.username" 
                        placeholder="username" 
                    />
                    <InputError :message="form.errors.username" />
                </div>
                
                <div class="grid gap-2">
                    <div class="flex items-center justify-between">
                        <Label for="password">Password</Label>
                        <TextLink 
                            v-if="canResetPassword" 
                            :href="route('password.request')" 
                            class="text-sm" 
                            :tabindex="5"
                        >
                            Forgot password?
                        </TextLink>
                    </div>
                    <Input 
                        id="password" 
                        type="password" 
                        required 
                        :tabindex="2" 
                        autocomplete="current-password" 
                        v-model="form.password" 
                        placeholder="Password" 
                    />
                    <InputError :message="form.errors.password" />
                </div>
                
                <div class="flex items-center justify-between" :tabindex="3">
                    <Label for="remember" class="flex items-center space-x-3">
                        <Checkbox id="remember" v-model="form.remember" :tabindex="4" />
                        <span>Remember me</span>
                    </Label>
                </div>
                
                <Button 
                    type="submit" 
                    class="mt-4 w-full" 
                    :tabindex="4" 
                    :disabled="form.processing"
                >
                    <LoaderCircle v-if="form.processing" class="h-4 w-4 animate-spin" />
                    Log in
                </Button>
            </div>
            
            <!-- <div class="text-center text-sm text-muted-foreground">
                Don't have an account? 
                <TextLink :href="route('register')" :tabindex="5">Sign up</TextLink>
            </div> -->
        </form>
    </AuthBase>
</template>