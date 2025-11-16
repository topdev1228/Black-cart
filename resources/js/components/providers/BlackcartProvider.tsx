import { BlackcartContext, Product, Program, Store, Subscription } from '../contexts/BlackcartContext';
import { useContext, useEffect, useRef, useState } from 'react';
import useStoreApi from '../../hooks/useStoreApi';
import useProgramApi from '../../hooks/useProgramApi';
import useStoreSettingsApi from '../../hooks/useStoreSettingsApi';
import useSubscriptionsApi from '../../hooks/useSubscriptionsApi';
import useProductApi from '../../hooks/useProductApi';

import { useNavigate } from 'react-router-dom';

export const BlackcartProvider = ({ children }) => {
    const {
        store: contextStore,
        program: contextProgram,
        storeSettings: contextStoreSettings,
        stage: contextStage,
        latestStage: contextLatestStage,
        product: contextProduct,
    } = useContext(BlackcartContext);
    const [store, setStore] = useState(contextStore);
    const [program, setProgram] = useState(contextProgram);
    const [stage, setStage] = useState(contextStage);
    const [latestStage, setLatestStage] = useState(contextLatestStage);
    const [storeSettings, setStoreSettings] = useState(contextStoreSettings);
    const [product, setProduct] = useState(contextProduct);
    const formRef = useRef<HTMLFormElement | null>(null);
    const { storeData } = useStoreApi();
    const { programData, updateProgram } = useProgramApi();
    const { storeSettingsData, updateStoreSettings, isLoading: storeSettingsLoading } = useStoreSettingsApi();
    const { appInstallationData, createSubscription } = useSubscriptionsApi();
    const [loading, setLoading] = useState(true);
    const { getProduct } = useProductApi();

    const { Provider } = BlackcartContext;
    const navigate = useNavigate();

    useEffect(() => {
        setLoading(storeSettingsLoading);
    }, [storeSettingsLoading]);

    useEffect(() => {
        setStore(storeData);
    }, [storeData]);

    useEffect(() => {
        setProgram(programData);
    }, [programData]);

    useEffect(() => {
        setStoreSettings(storeSettingsData);
    }, [storeSettingsData]);

    useEffect(() => {
        if (program?.id) {
            getProduct(program.id).then((product) => {
                setProduct(product);
            });
        }
    }, [program]);

    useEffect(() => {
        const activeSubscription = appInstallationData?.activeSubscriptions.find(
            (appInstallation) => appInstallation.status === 'active',
        );
        if (!appInstallationData) {
            navigate(window.location.pathname);
        } else {
            if (!activeSubscription) {
                navigate('/billing');
            } else if (storeSettings) {
                if (
                    storeSettings &&
                    storeSettings.settings &&
                    storeSettings.settings.stage &&
                    storeSettings.settings.stage.value
                ) {
                    if (!stage) {
                        setStage(storeSettings.settings.stage.value);
                    }
                    setLatestStage(storeSettings.settings.stage.value);

                    if (storeSettings.settings.stage.value === 'postLaunch') {
                        if (stage === 'showLaunchModal') {
                            navigate('/');
                        } else if (
                            window.location.pathname === '/storeSettings' ||
                            window.location.pathname === '/transactions' ||
                            window.location.pathname === '/'
                        ) {
                            navigate(window.location.pathname);
                        } else {
                            navigate('/');
                        }
                    } else {
                        navigate('/onboarding');
                    }
                } else {
                    setStage('checkoutConfigurations');
                    setLatestStage('checkoutConfigurations');
                    navigate('/onboarding');
                }
            }
        }
    }, [storeSettings, appInstallationData, storeSettingsLoading, navigate]);

    const setStoreAttribute = (index: string, value: any) => {
        if (!store) return;

        setStore((prevStore) => {
            if (!prevStore) return prevStore;
            return {
                ...prevStore,
                [index]: value,
            };
        });
    };

    const setProgramAttribute = (index: string, value: any) => {
        if (!program) return;
        setProgram((prevProgram) => {
            if (!prevProgram) return prevProgram;
            return {
                ...prevProgram,
                [index]: value,
            };
        });
    };

    const setStoreSettingAttribute = (index: string, value: any) => {
        setStoreSettings((prevStoreSettings) => {
            if (!prevStoreSettings) return prevStoreSettings;
            return {
                ...prevStoreSettings,
                settings: {
                    ...prevStoreSettings.settings,
                    [index]: {
                        name: index,
                        value: value,
                    },
                },
            };
        });
    };

    const saveForm = () => {
        if (stage === 'storefrontSetUp') {
            if (storeSettings) {
                storeSettings.settings.stage = {
                    name: 'stage',
                    value: 'launch',
                };
                setStoreSettingAttribute('stage', 'launch');
                updateStoreSettings(storeSettings)
                    .then((res) => {
                        setStage('launch');
                        if (latestStage === 'returnsConfiguration') {
                            setLatestStage('launch');
                        }
                    })
                    .catch((error) => {
                        console.log('Error sending data to blackcart', error);
                    });
            }
        }
        if (!formRef.current) return;
        formRef.current.dispatchEvent(new Event('submit', { cancelable: true, bubbles: true }));
    };

    const setThemeInstallClicked = () => {
        setStoreSettingAttribute('theme_install_clicked', true);
        updateStoreSettings(storeSettings);
    };

    const save = () => {
        if (!store || !program || !formRef.current) return;

        if (stage === 'postLaunch') {
            updateProgram(program);
            updateStoreSettings(storeSettings);
        }

        if (stage === 'checkoutConfigurations') {
            updateProgram(program)
                .then((res) => {
                    if (storeSettings) {
                        storeSettings.settings.stage = {
                            name: 'stage',
                            value: 'returnsConfigurations',
                        };
                    }
                    setStage('returnsConfigurations');
                    updateStoreSettings(storeSettings);
                    if (latestStage === 'checkoutConfigurations') {
                        setLatestStage('returnsConfigurations');
                    }
                })
                .catch((error) => {
                    console.log('Error sending data to blackcart', error);
                });
        }

        if (stage === 'returnsConfigurations') {
            if (storeSettings) {
                setStoreSettingAttribute('stage', 'storefrontSetUp');
                storeSettings.settings.stage = {
                    name: 'stage',
                    value: 'storefrontSetUp',
                };
                updateStoreSettings(storeSettings)
                    .then((res) => {
                        setStage('storefrontSetUp');
                        if (latestStage === 'returnsConfigurations') {
                            setLatestStage('storefrontSetUp');
                        }
                    })
                    .catch((error) => {
                        console.log('Error sending data to blackcart', error);
                    });
            }
        }
    };

    const launch = () => {
        if (stage !== 'launch') {
            return;
        }
        if (storeSettings) {
            const copyStoreSettings = { ...storeSettings };
            copyStoreSettings.settings.status = {
                name: 'status',
                value: 'active',
            };
            copyStoreSettings.settings.stage = {
                name: 'stage',
                value: 'postLaunch',
            };
            updateStoreSettings(copyStoreSettings)
                .then((res) => {
                    setStage('showLaunchModal');
                    navigate('/');
                })
                .catch((error) => {
                    console.log('Error sending data to blackcart', error);
                });
        }
    };

    const enableBilling = (): Promise<Subscription> => {
        return createSubscription()
            .then((res) => {
                return res;
            })
            .catch((error) => {
                throw error;
            });
    };

    const updateStage = (stage: string) => {
        if (storeSettings) {
            const copyStoreSettings = { ...storeSettings };
            copyStoreSettings.settings.stage = {
                name: 'stage',
                value: stage,
            };
            updateStoreSettings(copyStoreSettings).catch((error) => {
                console.log('Error sending data to blackcart', error);
            });
        }
    };

    return (
        <Provider
            value={{
                store,
                program,
                stage,
                storeSettings,
                product,
                latestStage,
                formRef,
                setProgram,
                setStoreSettings,
                setStoreAttribute,
                setProgramAttribute,
                setStoreSettingAttribute,
                setStage,
                setLatestStage,
                saveForm,
                save,
                launch,
                enableBilling,
                setThemeInstallClicked,
                updateStage,
            }}
        >
            {children}
        </Provider>
    );
};
