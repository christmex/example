<script setup lang="ts">
import { Link, usePage } from '@inertiajs/vue3';
import { Star } from '@lucide/vue';
import AppLogoIcon from '@/components/AppLogoIcon.vue';
import { Avatar, AvatarFallback } from '@/components/ui/avatar';
import { home } from '@/routes';

const page = usePage();
const name = page.props.name;

defineProps<{
    title?: string;
    description?: string;
}>();
</script>

<template>
    <div
        class="relative grid h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:grid-cols-2 lg:px-0"
    >
        <div
            class="relative hidden h-full flex-col justify-between overflow-hidden bg-linear-to-br from-[var(--chart-3)] via-primary to-[var(--chart-5)] p-10 text-primary-foreground lg:flex"
        >
            <div
                aria-hidden="true"
                class="absolute inset-0 [background-image:radial-gradient(currentColor_1px,transparent_1px)] [background-size:22px_22px] opacity-[0.08]"
            />
            <div
                aria-hidden="true"
                class="absolute -top-24 -right-24 size-96 rounded-full bg-primary-foreground/10 blur-3xl"
            />
            <div
                aria-hidden="true"
                class="absolute -bottom-32 -left-20 size-80 rounded-full bg-primary-foreground/5 blur-3xl"
            />

            <Link
                :href="home()"
                class="relative z-20 flex items-center gap-2 text-lg font-medium"
            >
                <AppLogoIcon class="size-8 fill-current" />
                {{ name }}
            </Link>

            <figure class="relative z-20 flex flex-col gap-6">
                <div class="flex gap-1" aria-hidden="true">
                    <Star
                        v-for="starIndex in 5"
                        :key="starIndex"
                        class="size-5 fill-current"
                    />
                </div>
                <blockquote
                    class="text-2xl leading-snug font-medium tracking-tight text-balance"
                >
                    &ldquo;This starter kit gave us a production-ready
                    foundation in minutes. Authentication, typed routes, and a
                    polished UI were all there on day one.&rdquo;
                </blockquote>
                <figcaption class="flex items-center gap-3">
                    <Avatar class="size-11 ring-1 ring-primary-foreground/20">
                        <AvatarFallback
                            class="bg-primary-foreground/15 font-medium text-primary-foreground"
                        >
                            JC
                        </AvatarFallback>
                    </Avatar>
                    <div class="flex flex-col">
                        <span class="font-medium">Jonathan Christian</span>
                        <span class="text-sm text-primary-foreground/70">
                            Lead Engineer, Example Labs
                        </span>
                    </div>
                </figcaption>
            </figure>
        </div>
        <div class="lg:p-8">
            <div
                class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]"
            >
                <div class="flex flex-col space-y-2 text-center">
                    <h1 class="text-xl font-medium tracking-tight" v-if="title">
                        {{ title }}
                    </h1>
                    <p class="text-sm text-muted-foreground" v-if="description">
                        {{ description }}
                    </p>
                </div>
                <slot />
            </div>
        </div>
    </div>
</template>
