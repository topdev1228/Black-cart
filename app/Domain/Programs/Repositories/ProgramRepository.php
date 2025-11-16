<?php
declare(strict_types=1);

namespace App\Domain\Programs\Repositories;

use App\Domain\Programs\Models\Program;
use App\Domain\Programs\Values\Collections\ProgramCollection;
use App\Domain\Programs\Values\Program as ProgramValue;

class ProgramRepository
{
    /**
     * @psalm-suppress InvalidReturnType
     * @psalm-suppress InvalidReturnStatement
     * @psalm-suppress InvalidArgument
     */
    public function all(): ProgramCollection
    {
        $program = Program::orderBy('created_at', 'desc')->first();
        if ($program === null) {
            return ProgramValue::collection(null);
        }

        return ProgramValue::collection([$program]);
    }

    public function getById(string $id): ProgramValue
    {
        return ProgramValue::from(Program::findOrFail($id));
    }

    public function update(string $id, array $updateProgramValues): ProgramValue
    {
        $program = Program::findOrFail($id);
        $program->update($updateProgramValues);

        return ProgramValue::from($program);
    }

    public function store(ProgramValue $programValue): ProgramValue
    {
        return ProgramValue::from(Program::create($programValue->toArray()));
    }
}
