import { TextField, Text, Divider, Card } from '@shopify/polaris';
import { useCallback, useContext, useEffect, useState } from 'react';
import { BlackcartContext } from '../contexts/BlackcartContext';
import { notEmpty, useField, useForm } from '@shopify/react-form';
import React from 'react';
import validator from 'validator';

function getTitle(): string {
    return 'Returns Configurations';
}

interface returnsConfigurationsProps {}

const ReturnsConfigurations = (props: returnsConfigurationsProps) => {
    const { formRef, storeSettings, setStoreSettingAttribute, save } = useContext(BlackcartContext);

    const { value: returnsPortalUrlValue, ...returnsPortalUrlFieldData } = useField({
        value: storeSettings?.settings.returnsPortalUrl?.value,
        validates: [
            notEmpty('A Returns Portal URL is required'),
            (value) => {
                if (value && !validator.isURL(value)) {
                    return 'Please enter a valid URL';
                }
            },
        ],
    });
    useEffect(() => {
        setStoreSettingAttribute('returnsPortalUrl', returnsPortalUrlValue);
    }, [returnsPortalUrlValue]);

    const { value: customerSupportEmailValue, ...customerSupportEmailFieldData } = useField({
        value: storeSettings?.settings.customerSupportEmail?.value,
        validates: [
            notEmpty('A Customer Support email or website is required'),
            (value) => {
                if (value && (!validator.isEmail(value) || !validator.isURL(value))) {
                    return 'Please enter a valid email or website';
                }
            },
        ],
    });
    useEffect(() => {
        setStoreSettingAttribute('customerSupportEmail', customerSupportEmailValue);
    }, [customerSupportEmailValue]);

    const { fields, submit } = useForm({
        fields: {
            returnsPortalUrl: {
                value: returnsPortalUrlValue,
                ...returnsPortalUrlFieldData,
            },
            customerSupportEmail: {
                value: customerSupportEmailValue,
                ...customerSupportEmailFieldData,
            },
        },
        async onSubmit(form) {
            save();
            return { status: 'success' };
        },
    });

    return (
        <Card>
            <form aria-label="form" ref={formRef} onSubmit={submit}>
                <Text variant="headingSm" as="h6">
                    How Returns Work
                </Text>
                <br />
                <Text variant="bodyMd" as="p" tone="subdued">
                    Shoppers will be notified via email that their try period has started when the items in their order
                    have been delivered. A link to the Returns URL you entered above will be included in that email. If
                    a return is initiated before the end of the try period, the shopper won't be charged. After the try
                    period expires, the shopper will be charged for any kept items (less any deposit paid at checkout).
                    <br />
                    <br />
                    Your customer service email will be included in the email sent to shoppers if there are any
                    questions or issues.
                </Text>
                <br />
                <Divider borderColor="border" />
                <br />
                <TextField
                    {...fields.returnsPortalUrl}
                    label="Return Portal URL"
                    placeholder="https://"
                    helpText="Add your existing Returns URL to direct customers to initiate a return for their Try Before You Buy item(s)."
                    onBlur={(e) => {
                        let value = (e?.target as HTMLInputElement).value;
                        if (
                            returnsPortalUrlValue &&
                            !returnsPortalUrlValue.startsWith('https://') &&
                            !returnsPortalUrlValue.startsWith('http://')
                        ) {
                            value = 'https://' + returnsPortalUrlValue;
                        }
                        setStoreSettingAttribute('returnsPortalUrl', value);
                    }}
                    autoComplete="off"
                />
                <br />
                <TextField
                    {...fields.customerSupportEmail}
                    label="Customer Support Email or Website"
                    type="email"
                    helpText="Set an email or website for customers with any questions regarding their Try Before You Buy orders."
                    autoComplete="off"
                />
                <br />
            </form>
        </Card>
    );
};

ReturnsConfigurations.getTitle = getTitle;
export default ReturnsConfigurations;
