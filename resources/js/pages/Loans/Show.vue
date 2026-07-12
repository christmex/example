<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Progress } from '@/components/ui/progress';
import { Spinner } from '@/components/ui/spinner';
import { dashboard } from '@/routes';
import { store } from '@/routes/loans/investments';

// Adjust these two if the platform currency is not British pounds.
const CURRENCY_LOCALE = 'en-GB';
const CURRENCY_SYMBOL = '£';

type LoanPayload = {
    id: string;
    business_name: string;
    amount_requested: string;
    amount_funded: string;
    is_fully_funded: boolean;
};

type InvestorPayload = {
    id: string;
    name: string;
    available_balance: string;
} | null;

const props = defineProps<{
    loan: LoanPayload;
    investor: InvestorPayload;
}>();

defineOptions({
    layout: {
        breadcrumbs: [{ title: 'Dashboard', href: dashboard() }],
    },
});

const form = useForm({ amount: '' });

const amountRequested = computed(() => Number(props.loan.amount_requested));
const amountFunded = computed(() => Number(props.loan.amount_funded));
const remaining = computed(() =>
    Math.max(amountRequested.value - amountFunded.value, 0),
);
const percentage = computed(() =>
    amountRequested.value > 0
        ? Math.min((amountFunded.value / amountRequested.value) * 100, 100)
        : 0,
);
// Floor (not round) so a not-quite-full loan never displays as "100.0%".
const percentageLabel = computed(() =>
    (Math.floor(percentage.value * 10) / 10).toFixed(1),
);
const isFullyFunded = computed(() => props.loan.is_fully_funded);

function formatMoney(value: string | number): string {
    return new Intl.NumberFormat(CURRENCY_LOCALE, {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }).format(Number(value));
}

function submitInvestment(): void {
    form.post(store(props.loan.id).url, {
        preserveScroll: true,
        only: ['loan', 'investor'],
        onSuccess: () => form.reset('amount'),
    });
}
</script>

<template>
    <Head :title="loan.business_name" />

    <div class="mx-auto flex w-full max-w-2xl flex-col gap-6 p-4">
        <Card>
            <CardHeader>
                <div class="flex items-start justify-between gap-4">
                    <div class="flex flex-col gap-1">
                        <CardTitle class="text-2xl">
                            {{ loan.business_name }}
                        </CardTitle>
                        <CardDescription>Business loan funding</CardDescription>
                    </div>
                    <Badge :variant="isFullyFunded ? 'default' : 'secondary'">
                        {{ isFullyFunded ? 'Fully funded' : 'Open' }}
                    </Badge>
                </div>
            </CardHeader>
            <CardContent class="flex flex-col gap-6">
                <div class="flex flex-col gap-2">
                    <div class="flex items-end justify-between">
                        <span class="text-sm text-muted-foreground"
                            >Funded</span
                        >
                        <span class="text-sm font-medium">
                            {{ percentageLabel }}%
                        </span>
                    </div>
                    <Progress :model-value="percentage" class="h-3" />
                    <div class="flex justify-between text-sm">
                        <span class="font-medium">
                            {{ CURRENCY_SYMBOL
                            }}{{ formatMoney(loan.amount_funded) }}
                        </span>
                        <span class="text-muted-foreground">
                            of {{ CURRENCY_SYMBOL
                            }}{{ formatMoney(loan.amount_requested) }}
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div class="flex flex-col gap-1 rounded-lg border p-3">
                        <span class="text-xs text-muted-foreground">
                            Remaining needed
                        </span>
                        <span class="text-lg font-semibold">
                            {{ CURRENCY_SYMBOL }}{{ formatMoney(remaining) }}
                        </span>
                    </div>
                    <div class="flex flex-col gap-1 rounded-lg border p-3">
                        <span class="text-xs text-muted-foreground">
                            Your balance
                        </span>
                        <span class="text-lg font-semibold">
                            {{
                                investor
                                    ? CURRENCY_SYMBOL +
                                      formatMoney(investor.available_balance)
                                    : '—'
                            }}
                        </span>
                    </div>
                </div>
            </CardContent>
        </Card>

        <Card>
            <CardHeader>
                <CardTitle>Fund this loan</CardTitle>
                <CardDescription v-if="investor">
                    Investing as {{ investor.name }}
                </CardDescription>
            </CardHeader>
            <CardContent>
                <p v-if="!investor" class="text-sm text-muted-foreground">
                    You do not have an investor account yet.
                </p>
                <p
                    v-else-if="isFullyFunded"
                    class="text-sm text-muted-foreground"
                >
                    This loan is fully funded. Thank you!
                </p>
                <form
                    v-else
                    class="flex flex-col gap-4"
                    @submit.prevent="submitInvestment"
                >
                    <div class="flex flex-col gap-2">
                        <Label for="amount">Amount</Label>
                        <Input
                            id="amount"
                            v-model="form.amount"
                            type="number"
                            step="0.01"
                            min="0"
                            inputmode="decimal"
                            placeholder="0.00"
                            :disabled="form.processing"
                            autofocus
                        />
                        <InputError :message="form.errors.amount" />
                    </div>
                    <Button type="submit" :disabled="form.processing">
                        <Spinner v-if="form.processing" />
                        Fund loan
                    </Button>
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-sm font-medium text-primary"
                    >
                        Investment submitted successfully.
                    </p>
                </form>
            </CardContent>
        </Card>
    </div>
</template>
