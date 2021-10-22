<?php
    class Company {
        private ?string $name = null;
        private ?string $address = null;
        private ?int $zip = null;
        private ?string $city = null;
        private ?string $activity = null;
        private ?string $vat = null;

        public function __construct (string $name, string $address, int $zip, string $city, string $activity, string $vat) {
            $this->name = $name;
            $this->address = $address;
            $this->zip = $zip;
            $this->city = $city;
            $this->activity = $activity;
            $this->vat = $vat;
        }

        public function getName (): string {
            return $this->name;
        }

        public function getAddress (): string {
            return $this->address;
        }

        public function getZip (): string {
            return $this->zip;
        }

        public function getCity (): string {
            return $this->city;
        }

        public function getActivity (): string {
            return $this->activity;
        }

        public function getVat (): string {
            return $this->vat;
        }

        public function __toString (): string {
            return $this->name . ' ' . $this->address . ' ' . $this->zip . ' ' . $this->city . ' ' . $this->activity . ' ' . $this->vat;
        }

        function StringLandcode (string $landcode): string {
            if ($landcode === 'FR') {
                return $this->zip . ' ' . $this->address . ' ' . $this->city . ' ' . 'FR';
            }
            else {
                return $this->zip . ' ' . $this->address . ' ' . $this->city . ' ' . 'BE';
            }
        }

    }