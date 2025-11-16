import { Card, Checkbox, FormLayout, TextField, Select, Badge } from '@shopify/polaris';
import { BlackcartContext } from '../contexts/BlackcartContext';
import { useContext, useCallback, useEffect } from 'react';
import { useForm, useField, notEmpty } from '@shopify/react-form';
import { useTranslation } from 'react-i18next';
import React from 'react';

function getTitle(): string {
    return 'Try Before You Buy Configurations';
}
interface checkoutConfigurationsProps {}

const CheckoutConfigurations = (props: checkoutConfigurationsProps) => {
    const { formRef, program, storeSettings, setProgramAttribute, save, updateStage } = useContext(BlackcartContext);
    const { t } = useTranslation();

    const { value: programNameValue, ...programNameFieldData } = useField({
        value: program?.name ?? '',
        validates: [
            notEmpty(t('The program name is required')),
            (programName) => {
                if (programName?.length && programName.length > 255) {
                    return t('The program name must be less than 50 characters');
                }
            },
        ],
    });
    useEffect(() => {
        setProgramAttribute('name', programNameValue);
    }, [programNameValue]);

    const { value: depositValue, ...depositValueData } = useField({
        value: program?.depositValue?.toString() ?? '',
        validates: [
            notEmpty(t('The deposit is required')),
            (depositValue) => {
                const depositValueInt = parseInt(depositValue);
                if (isNaN(depositValueInt) || depositValueInt < 0 || depositValueInt > 100) {
                    return t('The deposit must be between 0 and 100 %');
                }
            },
        ],
    });
    useEffect(() => {
        setProgramAttribute('depositValue', parseFloat(depositValue).toFixed(0).toString());
    }, [depositValue]);

    const { value: tryPeriodDays, ...tryPeriodDaysData } = useField({
        value: program?.tryPeriodDays?.toString() ?? '',
        validates: [
            notEmpty(t('Period length is required')),
            (tryPeriodDays) => {
                const tryPeriodDaysInt = parseInt(tryPeriodDays);
                if (isNaN(tryPeriodDaysInt) || tryPeriodDaysInt < 3) {
                    return t('The period length must be greater than 3');
                }
            },
        ],
    });

    useEffect(() => {
        setProgramAttribute('tryPeriodDays', tryPeriodDays);
    }, [tryPeriodDays]);

    const { value: dropOffDays, ...dropOffDaysData } = useField({
        value: program?.dropOffDays?.toString() ?? '',
        validates: [notEmpty(t('Drop off days is required'))],
    });

    useEffect(() => {
        setProgramAttribute('dropOffDays', dropOffDays);
    }, [dropOffDays]);

    const { value: maxTbybItems, ...maxTbybItemsData } = useField({
        value: program?.maxTbybItems?.toString() ?? '',
        validates: [
            (maxTbybItems) => {
                const maxTbybItemsInt = parseInt(maxTbybItems);
                if (!isNaN(maxTbybItemsInt) && program?.minTbybItems && maxTbybItemsInt < program?.minTbybItems) {
                    return t('The maximum number of items must be greater than the minimum');
                }
            },
        ],
    });
    useEffect(() => {
        setProgramAttribute('maxTbybItems', maxTbybItems);
    }, [maxTbybItems]);

    useEffect(() => {
        if (!storeSettings || !storeSettings.settings || !storeSettings.settings.stage) {
            updateStage('checkoutConfigurations');
        }
    }, []);
    const { fields, submit } = useForm({
        fields: {
            programName: {
                ...programNameFieldData,
                value: programNameValue,
            },
            depositValue: {
                ...depositValueData,
                value: depositValue,
            },
            tryPeriodDays: {
                ...tryPeriodDaysData,
                value: tryPeriodDays,
            },
            dropOffDays: {
                ...dropOffDaysData,
                value: dropOffDays,
            },
            minTbybItems: useField({
                value: program?.minTbybItems?.toString() ?? '',
                validates: [
                    notEmpty(t('The minimum number of items is required')),
                    (minTbybItems) => {
                        const minTbybItemsInt = parseInt(minTbybItems);
                        if (isNaN(minTbybItemsInt) || minTbybItemsInt < 1) {
                            return t('The minimum number of items must be greater than 1');
                        }
                    },
                ],
            }),
            maxTbybItems: {
                ...maxTbybItemsData,
                value: maxTbybItems,
            },
        },
        async onSubmit(form) {
            save();
            return { status: 'success' };
        },
    });

    const handleFieldChange = useCallback(
        (key, value) => {
            if (value === 'unlimited') {
                value = null;
            }
            setProgramAttribute(key, value);
        },
        [program, setProgramAttribute],
    );

    useEffect(() => {
        handleFieldChange('depositType', 'percentage');
    }, []);

    const optionValues = [
        { label: 'Unlimited', value: 'unlimited', prefix: 'Max' },
        { label: '1', value: '1', prefix: 'Max' },
        { label: '2', value: '2', prefix: 'Max' },
        { label: '3', value: '3', prefix: 'Max' },
        { label: '4', value: '4', prefix: 'Max' },
        { label: '5', value: '5', prefix: 'Max' },
        { label: '6', value: '6', prefix: 'Max' },
        { label: '7', value: '7', prefix: 'Max' },
        { label: '8', value: '8', prefix: 'Max' },
        { label: '9', value: '9', prefix: 'Max' },
        { label: '10', value: '10', prefix: 'Max' },
    ];
    return (
        <Card>
            <form aria-label="form" ref={formRef} onSubmit={submit}>
                <div style={{ paddingBottom: '20px' }}>
                    <FormLayout.Group>
                        <TextField
                            {...fields.programName}
                            label={t('Program Name')}
                            helpText={t(
                                'Program name will be displayed on the toggle or button on your PDP and as an item label in the cart.',
                            )}
                            autoComplete="off"
                        />
                        <TextField
                            {...fields.depositValue}
                            type="number"
                            label={
                                <span>
                                    {t('Deposit')} <Badge tone="attention">Recommended</Badge>
                                </span>
                            }
                            helpText={
                                <span>
                                    Deposit is a percentage of the price charged for each Try Before You Buy item(s).
                                    It's applied as a credit towards items that are kept or refunded if all items are
                                    returned.{' '}
                                    <b>
                                        A Deposit of 5-10% is recommended - checkout conversion is 34% higher with a
                                        deposit.
                                    </b>
                                </span>
                            }
                            suffix={t('%')}
                            autoComplete="off"
                            min={0}
                            max={100}
                            step={1}
                        />
                    </FormLayout.Group>
                </div>
                <FormLayout.Group>
                    <TextField
                        {...fields.tryPeriodDays}
                        type="number"
                        label={t('Try Period Length')}
                        helpText={
                            <span>
                                Starting from delivery, amount of time a customer has to try the product(s). Customer is
                                charged in full at the end of the try period unless a return occurs.
                                <b> A try period length of 7 days is recommend.</b>
                            </span>
                        }
                        suffix={t('days')}
                        autoComplete="off"
                        min={3}
                    />
                    <Select
                        {...fields.maxTbybItems}
                        label={t('Number of items')}
                        helpText={t(
                            "The maximum number of 'Try Before You Buy' items customers can add to their cart to be able to checkout. Select unlimited if no max.",
                        )}
                        options={optionValues}
                    />
                    <TextField
                        {...fields.dropOffDays}
                        type="number"
                        label={t('Drop off days extension')}
                        helpText={t(
                            'A fixed number of days to extend a trial by when the customer drops off their return to accomodate for any delays in processing the return in Shopify.',
                        )}
                        suffix={t('days')}
                        autoComplete="off"
                        min={0}
                    />
                </FormLayout.Group>
            </form>
        </Card>
    );
};

CheckoutConfigurations.getTitle = getTitle;
export default CheckoutConfigurations;
