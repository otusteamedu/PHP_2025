DELIMITER //

CREATE FUNCTION calculate_time_coefficient(start_time TIMESTAMP)
RETURNS DECIMAL(5, 2)
DETERMINISTIC
BEGIN
    DECLARE coefficient DECIMAL(5, 2);

    -- Извлекаем время из TIMESTAMP
    SET @time = TIME(start_time);

    -- Условие для определения коэффициента
    IF @time >= '10:00:00' AND @time < '11:00:00' THEN
        SET coefficient = 0.6;
    ELSEIF @time >= '18:00:00' AND @time < '24:00:00' THEN
        SET coefficient = 1.15;
    ELSE
        SET coefficient = 1.0; -- Значение по умолчанию, если не попадает в указанные диапазоны
    END IF;

    RETURN coefficient;
END //

DELIMITER ;