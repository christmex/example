<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Progress } from '@/components/ui/progress';
import { formatCurrency } from '@/lib/currency';
import { fundedPercentage } from '@/lib/loan';
import { index as loansIndex, show } from '@/routes/loans';

type LoanListItem = {
    id: string;
    business_name: string;
    amount_requested: string;
    amount_funded: string;
    is_fully_funded: boolean;
};

defineProps<{
    loans: LoanListItem[];
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Loans', href: loansIndex() }],
    },
});
</script>

<template>
    <Head title="Loans" />

    <div class="mx-auto flex w-full max-w-3xl flex-col gap-4 p-4">
        <div class="flex flex-col gap-1">
            <h1 class="text-2xl font-semibold">Loans</h1>
            <p class="text-sm text-muted-foreground">
                Pick a loan to fund from your investor balance.
            </p>
        </div>

        <p
            v-if="loans.length === 0"
            class="rounded-lg border border-dashed p-8 text-center text-sm text-muted-foreground"
        >
            No loans are available yet.
        </p>

        <Link
            v-for="loan in loans"
            :key="loan.id"
            :href="show(loan.id)"
            class="block transition hover:shadow-md"
        >
            <Card>
                <CardHeader>
                    <div class="flex items-start justify-between gap-4">
                        <CardTitle class="text-lg">
                            {{ loan.business_name }}
                        </CardTitle>
                        <Badge
                            :variant="
                                loan.is_fully_funded ? 'default' : 'secondary'
                            "
                        >
                            {{ loan.is_fully_funded ? 'Fully funded' : 'Open' }}
                        </Badge>
                    </div>
                </CardHeader>
                <CardContent class="flex flex-col gap-2">
                    <Progress
                        :model-value="
                            fundedPercentage(
                                loan.amount_funded,
                                loan.amount_requested,
                            )
                        "
                        class="h-2"
                    />
                    <div
                        class="flex justify-between text-sm text-muted-foreground"
                    >
                        <span class="font-medium text-foreground">
                            {{ formatCurrency(loan.amount_funded) }}
                        </span>
                        <span
                            >of
                            {{ formatCurrency(loan.amount_requested) }}</span
                        >
                    </div>
                </CardContent>
            </Card>
        </Link>
    </div>
</template>
