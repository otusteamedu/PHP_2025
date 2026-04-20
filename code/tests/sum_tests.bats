#!/usr/bin/env bats

@test "Ошибка при отсутствии аргументов" {
  run ./sum.sh
  [ "$status" -ne 0 ]
}

@test "Ошибка при одном аргументе" {
  run ./sum.sh 1
  [ "$status" -ne 0 ]
}

@test "Ошибка при трех аргументах" {
  run ./sum.sh 1 2 3
  [ "$status" -ne 0 ]
}

@test "В первом аргументе не число" {
  run ./sum.sh test1 2
  [ "$status" -ne 0 ]
}

@test "Во втором аргументе не число" {
  run ./sum.sh 1 test2
  [ "$status" -ne 0 ]
}

@test "Во втором аргументе не число-2" {
  run ./sum.sh 1 2.
  [ "$status" -ne 0 ]
}

@test "Во втором аргументе не число-3" {
  run ./sum.sh 1 .1
  [ "$status" -ne 0 ]
}

@test "В обоих аргументах не число" {
  run ./sum.sh test1 test2
  [ "$status" -ne 0 ]
}

@test "Сложение двух положительных чисел" {
  run ./sum.sh 2 3
  [ "$status" -eq 0 ]
  [ "$output" = "5" ]
}

@test "Сложение дробных чисел" {
  run ./sum.sh 1.5 2.3
  [ "$status" -eq 0 ]
  [ "$output" = "3.8" ]
}

@test "Сложение целого и дробного числа" {
  run ./sum.sh 5 2.3
  [ "$status" -eq 0 ]
  [ "$output" = "7.3" ]
}

@test "Сложение отрицательных чисел" {
  run ./sum.sh -2 -7
  [ "$output" = "-9" ]
}
