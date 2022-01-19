<?php

namespace App\Entity;

use App\Repository\RoomRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use \Datetime;
use Doctrine\Persistence\ManagerRegistry;


#[ORM\Entity(repositoryClass: RoomRepository::class)]
class Room
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 255)]
    private $name;

    #[ORM\Column(type: 'boolean')]
    private $onlyForPremiumMembers = false;

    #[ORM\OneToMany(mappedBy: 'roomId', targetEntity: Booking::class, fetch: "EAGER")]
    private $bookings;

    public function __construct(bool $premium)
    {
        $this->bookings = new ArrayCollection();
        $this->onlyForPremiumMembers = $premium;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOnlyForPremiumMembers(): ?bool
    {
        return $this->onlyForPremiumMembers;
    }

    public function setOnlyForPremiumMembers(bool $onlyForPremiumMembers): self
    {
        $this->onlyForPremiumMembers = $onlyForPremiumMembers;

        return $this;
    }


    function canBook(User $user): bool
    {
        return ($this->getOnlyForPremiumMembers() && $user->getPremiumMember()) || !$this->getOnlyForPremiumMembers();
    }


    function canBookTimeFrame(DateTime $start, DateTime $end): bool
    {
        $interval = $start->diff($end);
        $diffInMinutes = $interval->i;
        $diffInHours   = $interval->h;
        $diffInDays    = $interval->d;
        $diffInMonths  = $interval->m;
        $diffInYears   = $interval->y;

        $minutes = 0;
        $minutes += $diffInHours * 60;
        $minutes +=  $diffInMinutes;

        if ($diffInDays == 0 && $diffInMonths == 0 && $diffInYears == 0 && 0 < $minutes && $minutes <= 240) {
            return true;
        } else {
            return false;
        }
    }

    function canAfford(User $user, int $hour): bool
    {
        return ($user->getCredit() > $hour * 2);
    }

    public function reservedDates(ManagerRegistry $doctrine): array
    {
        $entityManager = $doctrine->getManager();
        $room = $entityManager->getRepository(Room::class)->find($this->getId());

        $bookings = $room->getBookings()->unwrap();
        $reservedDates = [];

        foreach ($bookings as &$value) {
            $reservedDates[] = ['start' => $value->getStartDate(), 'end' => $value->getEndDate()];
        }
        return $reservedDates;
    }

    public function isAvailable(DateTime $startDate, DateTime $endDate, array $reservedDates): bool
    {
        $check = true;
        if (count($reservedDates) != 0) {
            foreach ($reservedDates as &$value) {
                if ($startDate->getTimestamp() > $value['start']->getTimestamp() && $startDate->getTimestamp() < $value['end']->getTimestamp()) {
                    $check = false; // new start > old start AND new start < old end 
                } elseif ($endDate->getTimestamp() > $value['start']->getTimestamp() && $endDate->getTimestamp() < $value['end']->getTimestamp()) {
                    $check = false; // new end > old start AND new end < old end
                } elseif ($startDate->getTimestamp() <= $value['start']->getTimestamp() && $endDate->getTimestamp() > $value['end']->getTimestamp()) {
                    $check = false; // new start < old start AND new end > old end
                } elseif ($startDate->getTimestamp() > $value['start']->getTimestamp() && $endDate->getTimestamp() == $value['end']->getTimestamp()) {
                    $check = false; // new start > old start AND new end == old end
                } elseif ($startDate->getTimestamp() == $value['start']->getTimestamp() && $endDate->getTimestamp() == $value['end']->getTimestamp())
                    $check = false; // if dates of the bookings exactly match
            }
            // Check if end date is before end date
            if ($startDate->getTimestamp() > $endDate->getTimestamp()) {
                $check = false;
            }
            return $check;
        } else {
            return $check;
        }
    }

    /**
     * @return Collection|Booking[]
     */
    public function getBookings(): Collection
    {
        return $this->bookings;
    }

    public function addBooking(Booking $booking): self
    {
        if (!$this->bookings->contains($booking)) {
            $this->bookings[] = $booking;
            $booking->setRoomId($this);
        }

        return $this;
    }

    public function removeBooking(Booking $booking): self
    {
        if ($this->bookings->removeElement($booking)) {
            // set the owning side to null (unless already changed)
            if ($booking->getRoomId() === $this) {
                $booking->setRoomId(null);
            }
        }

        return $this;
    }
}
