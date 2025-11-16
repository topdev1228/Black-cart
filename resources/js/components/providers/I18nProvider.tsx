

export const I18n = ({ children }) => {
    return (<I18nContext.Provider>
        {children}
    </I18nContext.Provider>);
}

const locale = getUserLocale();

const [i18n] = useI18n();

function formatMoney(money: number) {

}
