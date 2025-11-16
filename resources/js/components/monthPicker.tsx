import { Button, OptionList, Popover } from '@shopify/polaris';
import { useEffect, useState } from 'react';
import CalendarIcon from './calendarIcon';

interface DatePickerProps {
    setDateRangeSelected: (array) => void;
    dates: string[];
}

function MonthPicker(props: DatePickerProps) {
    const currentDate = new Date()
        .toLocaleString('en-US', { month: 'long', year: 'numeric' })
        .toLowerCase()
        .replace(' ', '-');
    const defaultRange = [formatDatePickerValue(currentDate)];
    const [ranges, setRanges] = useState(defaultRange);
    const [selected, setSelected] = useState(ranges[0]);
    const [popoverActive, setPopoverActive] = useState(false);

    useEffect(() => {
        if (props.dates.length <= 0) {
            return;
        }
        const ranges = [];
        for (let i = 0; i < props.dates.length; i++) {
            ranges.push(formatDatePickerValue(props.dates[i]));
        }

        setRanges(ranges);
    }, [props.dates]);

    //dateString in the format of january-2024
    function formatDatePickerValue(dateString) {
        let title = dateString.split('-')[0];
        title = title.charAt(0).toUpperCase() + title.slice(1);
        return {
            title: title,
            alias: dateString,
            //TODO: will be used if we ever introduce metrics by day
            period: {
                since: '',
                until: '',
            },
        };
    }

    function onChangeListener(value): void {
        const range = ranges.find((range) => range.alias === value[0]);
        setSelected(range);
        props.setDateRangeSelected([range.alias]);
        setPopoverActive(false);
    }

    return (
        <Popover
            autofocusTarget="none"
            preferredAlignment="left"
            preferInputActivator={false}
            preferredPosition="below"
            activator={
                <Button onClick={() => setPopoverActive(!popoverActive)} icon={CalendarIcon}>
                    {selected?.title}
                </Button>
            }
            active={popoverActive}
        >
            <OptionList
                options={ranges.map((range) => ({
                    value: range.alias,
                    label: range.title,
                }))}
                selected={selected?.alias}
                onChange={(value) => {
                    onChangeListener(value);
                }}
            />
        </Popover>
    );
}

export default MonthPicker;
