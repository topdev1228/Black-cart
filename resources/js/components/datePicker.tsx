import { Button, OptionList, Popover, DatePicker as DatePickerPolaris } from '@shopify/polaris';
import { useEffect, useState, useCallback } from 'react';
import CalendarIcon from './calendarIcon';

interface DatePickerProps {
    setDateRangeSelected: (array) => void; // This is used to set the date range selected from the DatePicker
    dates: []; // This is used for the default dates which is the beginning of the current month to the current date
}

function DatePicker(props: DatePickerProps) {
    const [selectedDates, setSelectedDates] = useState({
        start: new Date(props.dates[0]),
        end: new Date(props.dates[1]),
    });

    const [{ month, year }, setDate] = useState({
        month: new Date(props.dates[0]).getMonth() - 1,
        year: new Date(props.dates[0]).getFullYear(),
    });
    const [popoverActive, setPopoverActive] = useState(false);

    function onChangeListener(value): void {
        let startDate = new Date(value.start);
        let endDate = new Date(value.end);

        setSelectedDates({ start: startDate, end: endDate });
        props.setDateRangeSelected([startDate.toISOString(), endDate.toISOString()]);

        if (startDate.toISOString() !== endDate.toISOString()) {
            setPopoverActive(false);
        }
    }

    const handleMonthChange = useCallback((month: number, year: number) => setDate({ month, year }), []);

    return (
        <Popover
            autofocusTarget="none"
            preferredAlignment="left"
            preferInputActivator={false}
            preferredPosition="below"
            fluidContent={true}
            activator={
                <Button onClick={() => setPopoverActive(!popoverActive)} icon={CalendarIcon}>
                    {selectedDates.start.toLocaleDateString() + ' - ' + selectedDates.end.toLocaleDateString()}
                </Button>
            }
            active={popoverActive}
        >
            <DatePickerPolaris
                month={month}
                year={year}
                onChange={onChangeListener}
                onMonthChange={handleMonthChange}
                selected={selectedDates}
                multiMonth
                disableDatesBefore={new Date('Jan 01 2024 00:00:00 GMT-0500 (EST)')}
                disableDatesAfter={new Date(props.dates[1])}
                allowRange
            />
        </Popover>
    );
}

export default DatePicker;
