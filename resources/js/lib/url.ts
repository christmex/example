import type { InertiaLinkProps } from '@inertiajs/vue3';

/**
 * Resolve an Inertia `href` (string or Wayfinder route object) to a URL string.
 *
 * Kept in its own module — NOT in `lib/utils.ts` — because `lib/utils.ts` is a
 * shadcn-vue managed file that the CLI overwrites when a preset is (re)applied.
 */
export function toUrl(href: NonNullable<InertiaLinkProps['href']>): string {
    return typeof href === 'string' ? href : href.url;
}
